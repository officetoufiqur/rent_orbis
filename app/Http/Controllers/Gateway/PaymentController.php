<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\PlanLog;
use App\Models\RentLog;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function deposit() {
        if (!session()->has('rent_id') && !session()->has('plan_id')) {
            $notify[] = ['error', 'Invalid request!'];
            return back()->withNotify($notify);
        }
        $rentId = session('rent_id');
        $planId = session('plan_id');

        $amount = 0;
        if ($rentId) {
            $rentLog = RentLog::where('id', $rentId)->first();
            $amount  = getAmount($rentLog->price);
        } else {
            $planLog = PlanLog::where('id', $planId)->first();
            $amount  = getAmount($planLog->price);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Payment Methods';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'amount'));
    }

    public function depositInsert(Request $request) {
        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
            'currency' => 'required',
        ]);

        if (!session()->has('rent_id') && !session()->has('plan_id')) {
            $notify[] = ['error', 'Invalid request!'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $amount = 0;
        if (session('plan_id')) {
            $planLog = PlanLog::where('id', session('plan_id'))->first();
            $amount  = getAmount($planLog->price);
        }

        if (session('rent_id')) {
            $rentLog = RentLog::where('id', session('rent_id'))->first();
            $amount  = getAmount($rentLog->price);
        }

        if (!$amount) {
            $notify[] = ['error', 'Invalid request!'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $amount || $gate->max_amount < $amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $charge      = $gate->fixed_charge + ($amount * $gate->percent_charge / 100);
        $payable     = $amount + $charge;
        $finalAmount = $payable * $gate->rate;

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->rent_log_id     = session('rent_id') ?? 0;
        $data->plan_log_id     = session('plan_id') ?? 0;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amount    = $finalAmount;
        $data->btc_amount      = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->success_url     = urlPath('user.deposit.history');
        $data->failed_url      = urlPath('user.deposit.history');
        $data->save();

        session()->forget('plan_id');
        session()->forget('rent_id');

        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function appDepositConfirm($hash) {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositConfirm() {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias;
        $new     = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        if (!isset($data->error) && !isset($data->redirect)) {
            $this->userDataUpdate($deposit); 
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null) {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            $user = User::find($deposit->user_id);

            if ($deposit->rent_log_id) {
                $rentLog         = RentLog::where('id', $deposit->rent_log_id)->first();
                $amount          = $rentLog->price;
                $rentLog->status = Status::YES;
                $rentLog->trx    = $deposit->trx;
                $rentLog->save();
            } else {
                $planLog         = PlanLog::where('id', $deposit->plan_log_id)->first();
                $amount          = $planLog->price;
                $planLog->status = Status::YES;
                $planLog->trx    = $deposit->trx;
                $planLog->save();
            }

            $user->balance += $amount;
            $user->save();

            $methodName = $deposit->methodName();

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'Deposit successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name'     => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amount, currencyFormat: false),
                'amount'          => showAmount($deposit->amount, currencyFormat: false),
                'charge'          => showAmount($deposit->charge, currencyFormat: false),
                'rate'            => showAmount($deposit->rate, currencyFormat: false),
                'trx'             => $deposit->trx,
                'post_balance'    => showAmount($user->balance),
            ]);

            //Forget session
            session()->forget(['rent_id', 'plan_id']);
        }
    }

    public function manualDepositConfirm() {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request) {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amount, currencyFormat: false),
            'amount'          => showAmount($data->amount, currencyFormat: false),
            'charge'          => showAmount($data->charge, currencyFormat: false),
            'rate'            => showAmount($data->rate, currencyFormat: false),
            'trx'             => $data->trx,
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }

}
