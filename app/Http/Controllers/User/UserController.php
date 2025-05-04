<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Location;
use App\Models\Plan;
use App\Models\PlanLog;
use App\Models\Rating;
use App\Models\RentLog;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    public function home() {
        $pageTitle = 'Dashboard';

        $user = auth()->user();
        //Vehicle booking
        $data['total_vehicle_booking']     = RentLog::active()->where('user_id', $user->id)->count();
        $data['upcoming_vehicle_booking']  = RentLog::active()->where('user_id', $user->id)->upcoming()->count();
        $data['running_vehicle_booking']   = RentLog::active()->where('user_id', $user->id)->running()->count();
        $data['completed_vehicle_booking'] = RentLog::active()->where('user_id', $user->id)->completed()->count();

        //Plan booking
        $data['total_plan_booking']     = PlanLog::active()->where('user_id', $user->id)->count();
        $data['upcoming_plan_booking']  = PlanLog::active()->where('user_id', $user->id)->upcoming()->count();
        $data['running_plan_booking']   = PlanLog::active()->where('user_id', $user->id)->running()->count();
        $data['completed_plan_booking'] = PlanLog::active()->where('user_id', $user->id)->completed()->count();

        $deposits = auth()->user()->deposits()->with(['gateway', 'rentLog', 'planLog'])->orderBy('id', 'desc')->take(10)->get();

        return view('Template::user.dashboard', compact('pageTitle', 'user', 'data', 'deposits'));
    }

    public function depositHistory(Request $request) {
        $pageTitle = 'Payment History';
        $deposits  = auth()->user()->deposits()->searchable(['trx', 'planLog.plan:name', 'rentLog.vehicle:name'])->with(['gateway', 'rentLog', 'planLog'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function userData() {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle  = 'User Data';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request) {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;

        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.home');
    }

    public function addDeviceToken(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash) {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    //vehicle Booking

    public function vehicleBooking($id, $slug) {
        if (!auth()->check()) {
            $notify[] = ['error', 'Please login to continue!'];
            return back()->withNotify($notify);
        }
        $pageTitle = 'Vehicle Booking';
        $vehicle   = Vehicle::active()->where('id', $id)->firstOrFail();
        $locations = Location::active()->orderBy('name')->get();
        return view('Template::vehicles.booking', compact('vehicle', 'pageTitle', 'locations'));
    }

    public function bookingConfirm(Request $request, $id) {
        $request->validate([
            'pick_location' => 'required|integer', Rule::exists('locations', 'id')->where(function ($location) {
                $location->where('status', Status::ENABLE);
            }),

            'drop_location' => 'required|integer', Rule::exists('locations', 'id')->where(function ($location) {
                $location->where('status', Status::ENABLE);
            }) . 'not_in:' . $request->pick_location,

            'pick_time'     => 'required|date_format:Y-m-d h:i A|after_or_equal:today',
            'drop_time'     => 'required|date_format:Y-m-d h:i A|after_or_equal:' . $request->pick_time,
        ], [
            'drop_location.not_in' => 'Please choose different location!',
        ]);

        $vehicle = Vehicle::active()->where('id', $id)->firstOrFail();

        if ($vehicle->booked()) {
            $notify[] = ['error', 'Already vehicle is booked!'];
            return back()->withNotify($notify);
        }

        $pickTime = Carbon::parse($request->pick_time);
        $dropTime = Carbon::parse($request->drop_time);

        $totalDays  = $pickTime->diffInDays($dropTime);
        $totalPrice = $vehicle->price * $totalDays;

        $rent                = new RentLog();
        $rent->user_id       = auth()->id();
        $rent->vehicle_id    = $vehicle->id;
        $rent->pick_location = $request->pick_location;
        $rent->drop_location = $request->drop_location;
        $rent->pick_time     = $pickTime;
        $rent->drop_time     = $dropTime;
        $rent->price         = getAmount($totalPrice);
        $rent->save();

        session(['rent_id' => $rent->id]);

        return to_route('user.deposit.index');
    }

    public function planBooking(Request $request, $id, $slug) {
        $request->validate([
            'location_id' => ['required', 'integer', Rule::exists('locations', 'id')->where(function ($query) {
                $query->where('status', Status::ENABLE);
            })],
            'pick_time'   => 'required|date_format:Y-m-d h:i A|after_or_equal:today',
        ]);
        $plan = Plan::active()->where('id', $id)->firstOrFail();

        $planLog                = new PlanLog();
        $planLog->user_id       = auth()->id();
        $planLog->plan_id       = $plan->id;
        $planLog->pick_location = $request->location_id;
        $planLog->pick_time     = Carbon::parse($request->pick_time);
        $planLog->drop_time     = Carbon::parse($request->pick_time)->addDays($plan->days);
        $planLog->price         = getAmount($plan->price);
        $planLog->save();

        session(['plan_id' => $planLog->id]);
        return to_route('user.deposit.index');
    }

    // Rating
    public function rating(Request $request, $id) {
        $request->validate([
            'rating'  => 'required|integer|min:1|in:1,2,3,4,5',
            'comment' => 'nullable|string',
        ]);
        $user = auth()->user();
        if (!$user) {
            $notify[] = ['error', 'Login is required!'];
            return back()->withNotify($notify);
        }

        $vehicle = Vehicle::active()->where('id', $id)->first();
        if (!$vehicle) {
            $notify[] = ['error', 'Invalid action!'];
            return back()->withNotify($notify);
        }

        $exist = Rating::where('user_id', $user->id)->where('vehicle_id', $id)->exists();
        if ($exist) {
            $notify[] = ['error', 'Already reviews & rating this vehicle!'];
            return back()->withNotify($notify);
        }

        $rating             = new Rating();
        $rating->user_id    = $user->id;
        $rating->vehicle_id = $id;
        $rating->rating     = $request->rating;
        $rating->comment    = $request->comment;
        $rating->save();

        $notify[] = ['success', 'Thanks for your rating!'];
        return back()->withNotify($notify);
    }

    public function vehicleBookingLog() {
        $pageTitle   = 'Vehicle Booking Log';
        $bookingLogs = RentLog::active()->where('user_id', auth()->id())->with(['vehicle', 'user', 'pickUpLocation', 'dropUpLocation'])->latest()->paginate(getPaginate());
        return view('Template::user.vehicle_booking_log', compact('pageTitle', 'bookingLogs'));
    }

    public function planBookingLog() {
        $pageTitle   = 'Plan Booking Log';
        $bookingLogs = PlanLog::active()->where('user_id', auth()->id())->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        return view('Template::user.plan_booking_log', compact('pageTitle', 'bookingLogs'));
    }
}
