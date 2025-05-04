@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="single-section pt-120 pb-120 bg--section">
        <div class="container">
            <h4 class="mb-4">@lang('You have selected this car')</h4>
            <div class="row gy-5">
                <div class="col-lg-5">
                    <div class="slider-top owl-theme owl-carousel border--dashed">
                        @foreach ($vehicle->images ?? [] as $image)
                            <div class="car__rental-thumb w-100 bg--body p-0">
                                <img src="{{ getImage(getFilePath('vehicle') . '/' . @$image, getFileSize('vehicle')) }}" alt="">
                            </div>
                        @endforeach
                    </div>
                    <div class="slider-bottom owl-theme owl-carousel mt-4">
                        @foreach ($vehicle->images ?? [] as $image)
                            <div class="rental__thumbnails bg--body">
                                <img src="{{ getImage(getFilePath('vehicle') . '/' . @$image, getFileSize('vehicle')) }}" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="book__wrapper bg--body border--dashed mb-4">
                        <form class="book--form row gx-3 gy-4 g-md-4" method="post" action="{{ route('user.vehicle.booking.confirm', $vehicle->id) }}">
                            @csrf

                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="pick-point" class="form--label">
                                        <i class="las la-street-view"></i> @lang('Pick Up Point')
                                    </label>
                                    <select name="pick_location" id="pick-point" class="form-control form--control select2" required>
                                        <option value="" selected disabled>@lang('Pick up point')</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ @$location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="form--label">
                                        <i class="las la-street-view"></i> @lang('Drop of Point')
                                    </label>
                                    <select name="drop_location" id="drop-point" class="form-control form--control select2" required>
                                        <option value="" selected disabled>@lang('Drop of Point')</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ @$location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label for="start-date" class="form--label">
                                        <i class="las la-calendar-alt"></i> @lang('Pick Up Date & Time')
                                    </label>
                                    <input type="text" name="pick_time" placeholder="@lang('Pick Up Date & Time')" id="dateAndTimePicker" autocomplete="off" data-position="top left" class="form-control form--control pick_time" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label class="form--label">
                                        <i class="las la-calendar-alt"></i> @lang('Drop of Date & Time')
                                    </label>
                                    <input type="text" name="drop_time" placeholder="@lang('Drop of Date & Time')" id="dateAndTimePicker2" autocomplete="off" data-position="top left" class="form-control form--control" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="booking-costs mb-4">
                                    @lang('You will be charged') <span class="total_amount text--danger">{{ showAmount($vehicle->price) }}</span> @lang('to book this')
                                    {{ $vehicle->name }} @lang('for') <span class="total_days">1 (@lang('One days.')) </span> @lang('Please confirm to your booking.')
                                </div>
                                <div class="form-group">
                                    <button class="btn cmn--btn form--control bg--base w-100" type="submit">@lang('Book Now')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush
@push('script')
    <script>
        (function($) {
            "use strict"
            $(document).on('click', '.plan_modal', function() {
                var url = $(this).data('url');
                $('.planForm').attr('action', url);
            });

            $('.select2modal').select2({
                dropdownParent: '#planModal'
            });
            $('input[name="pick_time"], input[name="drop_time"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                timePicker: true,
                timePicker24Hour: false,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10),
                locale: {
                    format: 'Y-MM-DD hh:mm A'
                }
            }, function(start, end, label) {
                console.log("Selected start date: ", start.format('Y-MM-DD hh:mm A'));
                $(this.element).val(start.format('Y-MM-DD hh:mm A'));
            });
        })(jQuery)
    </script>
@endpush
