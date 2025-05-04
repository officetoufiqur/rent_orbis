<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanLog;
use Illuminate\Http\Request;

class ManagePlanController extends Controller
{
    public function index()
    {
        $plans = Plan::latest()->paginate(getPaginate());
        $pageTitle = 'All Plans';
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function add()
    {
        $pageTitle = 'Add plan';
        return view('admin.plan.add', compact('pageTitle'));
    }

    public function store(Request $request, $id= 0)
    {
        $isRequired = $id ? 'nullable' : 'required';
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|gt:0',
            'days' => 'required|integer|gt:0',
            'included' => "$isRequired|array",
            'included.*' => 'required|string',
            'excluded' => 'nullable|array',
            'excluded.*' => 'required|string',
        ]);


        if($id){
            $plan = plan::findOrFail($id);
            $notification = 'Plan updated Successfully!';
        }else{

            $plan = new plan();
            $notification = 'Plan Added Successfully!';
        }

        $plan->name = $request->name;
        $plan->price = $request->price;
        $plan->days = $request->days;
        $plan->included = $request->included;
        $plan->excluded = $request->excluded;
        $plan->save();

        $notify[] = ['success',  $notification];
        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        $pageTitle = 'Edit plan';
        return view('admin.plan.edit', compact('pageTitle', 'plan'));
    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }


    //Booking Log
    public function bookingLog()
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'Plan Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
    public function upcomingBookingLog()
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->upcoming()->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'Plan Upcoming Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
    public function runningBookingLog()
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->running()->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'Plan Running Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
    public function completedBookingLog()
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->completed()->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'Plan Completed Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }

    public function userBookingLog($id)
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->where('user_id', $id)->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'User Plan Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
    public function userUpcomingBookingLog($id)
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->where('user_id', $id)->upcoming()->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'User Plan Upcoming Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
    public function userRunningBookingLog($id)
    {
        $planLogs = PlanLog::active()->searchable(['plan:name','user:username', 'trx', 'price'])->where('user_id', $id)->running()->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'User Plan Running Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
    public function userCompletedBooking_log($id)
    {
        $planLogs = PlanLog::active()->where('user_id', $id)->completed()->with(['plan', 'user', 'pickUpLocation'])->latest()->paginate(getPaginate());
        $pageTitle = 'User Plan Completed Booking Log';
        return view('admin.plan.booking_log', compact('pageTitle', 'planLogs'));
    }
}
