@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.plans.store', $plan->id) }}" method="post">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ $plan->name }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Price Per Ride')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="price" step="any" value="{{ getAmount($plan->price) }}" required>
                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Number Of Days')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="days"  value="{{ $plan->days }}" required>
                                         <div class="input-group-text">@lang('Days')</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="card">
                                    <h5 class="card-header bg--primary d-flex justify-content-between">@lang('Plan Included')
                                        <button type="button" class="btn btn-sm btn-outline-light newIncludedBtn">
                                            <i class="las la-plus"></i>@lang('Add New')
                                        </button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="row addedIncludedFields">
                                            @foreach($plan->included ?? [] as $in)
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 removeIncludedInput">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" name="included[]" class="form-control" value="{{ $in }}" required>
                                                        <button type="button" class="input-group-text removeIncludedFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="card">
                                    <h5 class="card-header bg--primary d-flex justify-content-between">@lang('Plan Excluded')
                                        <button type="button" class="btn btn-sm btn-outline-light newExcludeddBtn">
                                            <i class="las la-plus"></i>@lang('Add New')
                                        </button>
                                    </h5>

                                    <div class="card-body">
                                        <div class="row addedExcludedFields">
                                            @foreach($plan->excluded ?? [] as $ex)
                                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 removeIncludedInput">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" name="excluded[]" class="form-control" value="{{ $ex }}" required>
                                                        <button type="button" class="input-group-text removeIncludedFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </div>
                   
                </form>
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.plans.index') }}" />
@endpush
@push('script')
    <script>
        (function ($) {
            "use strict";
            //----- Add Included fields-------//
            $('.newIncludedBtn').on('click', function() {
                $(".addedIncludedFields").append(`
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 removeIncludedInput">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="included[]" class="form-control" placeholder="@lang('Enter plan included facility')" required>
                            <button type="button" class="input-group-text removeIncludedFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                `)
            });

            $(document).on('click', '.removeIncludedFile', function() {
                $(this).closest('.removeIncludedInput').remove();
            });

            //----- Add Excluded fields-------//
            $('.newExcludeddBtn').on('click', function() {
                $(".addedExcludedFields").append(`
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 removeExcludedInput">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="excluded[]" class="form-control" placeholder="@lang('Enter plan excluded facility')" required>
                            <button type="button" class="input-group-text removeExcludedFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                `)
            });

            $(document).on('click', '.removeExcludedFile', function() {
                $(this).closest('.removeExcludedInput').remove();
            });

        })(jQuery);
    </script>
@endpush

