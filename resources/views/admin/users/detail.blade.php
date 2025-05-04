@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12">
            <div class="row gy-4 mt-2">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.vehicles.user.booking.log', $user->id) }}"
                        title="Total Vehicle Booking"
                        icon="las la-car-side"
                        value="{{ $widget['total_vehicle_booking'] }}"
                        bg="12"
                        type="2"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.vehicles.user.booking.log.upcoming', $user->id) }}"
                        title="Upcoming Vehicle Booking"
                        icon="las la-hourglass-half"
                        value="{{ $widget['upcoming_vehicle_booking'] }}"
                        bg="13"
                        type="2"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.vehicles.user.booking.log.running', $user->id) }}"
                        title="Running Vehicle Booking"
                        icon="la la-spinner"
                        value="{{ $widget['running_vehicle_booking'] }}"
                        bg="14"
                        type="2"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.vehicles.user.booking.log.completed', $user->id) }}"
                        title="Complted Vehicle Booking"
                        icon="las la-check-circle"
                        value="{{ $widget['completed_vehicle_booking'] }}"
                        bg="success"
                        type="2"
                    />
                </div>
            </div>

            <div class="row gy-4 mt-2">
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.plans.user.booking.log',$user->id) }}"
                        title="Total Plan Booking"
                        icon="las la-money-bill-wave-alt"
                        value="{{ $widget['total_plan_booking'] }}"
                        bg="10"
                        type="2"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.plans.user.booking.upcoming.log',$user->id) }}"
                        title="Upcoming Plan Booking"
                        icon="las la-clipboard-check"
                        value="{{ $widget['upcoming_plan_booking'] }}"
                        bg="8"
                        type="2"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.plans.user.booking.running.log',$user->id) }}"
                        title="Running Plan Booking"
                        icon="la la-spinner"
                        value="{{ $widget['running_plan_booking'] }}"
                        bg="primary"
                        type="2"
                    />
                </div>
                <div class="col-xxl-3 col-sm-6">
                    <x-widget
                        style="7"
                        link="{{ route('admin.plans.user.booking.completed.log',$user->id) }}"
                        title="Completed Plan Booking"
                        icon="las la-check-circle"
                        value="{{ $widget['completed_plan_booking'] }}"
                        bg="success"
                        type="2"
                    />
                </div>
            </div>
           

            <div class="d-flex flex-wrap gap-3 mt-4">
                <div class="flex-fill">
                    <a href="{{ route('admin.deposit.list',$user->id) }}" class="btn btn--success btn--shadow w-100 btn-lg">
                        <i class="las la-wallet"></i> @lang('Payment') {{ showAmount($totalDeposit) }}
                    </a>
                </div>
                <div class="flex-fill">
                    <a href="{{route('admin.report.login.history')}}?search={{ $user->username }}" class="btn btn--primary btn--shadow w-100 btn-lg">
                        <i class="las la-list-alt"></i>@lang('Logins')
                    </a>
                </div>

                <div class="flex-fill">
                    <a href="{{ route('admin.users.notification.log',$user->id) }}" class="btn btn--secondary btn--shadow w-100 btn-lg">
                        <i class="las la-bell"></i>@lang('Notifications')
                    </a>
                </div>
                <div class="flex-fill">
                    @if($user->status == Status::USER_ACTIVE)
                    <button type="button" class="btn btn--warning btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                        <i class="las la-ban"></i>@lang('Ban User')
                    </button>
                    @else
                    <button type="button" class="btn btn--success btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                        <i class="las la-undo"></i>@lang('Unban User')
                    </button>
                    @endif
                </div>
            </div>


            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Information of') {{$user->fullname}}</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.users.update',[$user->id])}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required value="{{$user->firstname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required value="{{$user->lastname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email') </label>
                                    <input class="form-control" type="email" name="email" value="{{$user->email}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number') </label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+{{ $user->dial_code }}</span>
                                        <input type="number" name="mobile" value="{{ $user->mobile }}" id="mobile" class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address" value="{{@$user->address}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{@$user->city}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{@$user->state}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip" value="{{@$user->zip}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country') <span class="text--danger">*</span></label>
                                    <select name="country" class="form-control select2">
                                        @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}" @selected($user->country_code == $key)>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-4 col-12">
                                <div class="form-group">
                                    <label>@lang('Email Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                           data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev"
                                           @if($user->ev) checked @endif>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="form-group">
                                    <label>@lang('Mobile Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                           data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv"
                                           @if($user->sv) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="form-group">
                                    <label>@lang('2FA Verification') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if($user->ts) checked @endif>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($user->status == Status::USER_ACTIVE) @lang('Ban User') @else @lang('Unban User') @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{route('admin.users.status',$user->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if($user->status == Status::USER_ACTIVE)
                        <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</h6>
                        <div class="form-group">
                            <label>@lang('Reason')</label>
                            <textarea class="form-control" name="reason" rows="4" required></textarea>
                        </div>
                        @else
                        <p><span>@lang('Ban reason was'):</span></p>
                        <p>{{ $user->ban_reason }}</p>
                        <h4 class="text-center mt-3">@lang('Are you sure to unban this user?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if($user->status == Status::USER_ACTIVE)
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        @else
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('admin.users.login',$user->id)}}" target="_blank" class="btn btn-sm btn-outline--primary" ><i class="las la-sign-in-alt"></i>@lang('Login as User')</a>
@endpush

@push('script')
<script>
    (function($){
    "use strict"


        $('.bal-btn').on('click',function(){

            $('.balanceAddSub')[0].reset();

            var act = $(this).data('act');
            $('#addSubModal').find('input[name=act]').val(act);
            if (act == 'add') {
                $('.type').text('Add');
            }else{
                $('.type').text('Subtract');
            }
        });

        let mobileElement = $('.mobile-code');
        $('select[name=country]').on('change',function(){
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

    })(jQuery);
</script>
@endpush
