@php
    $booking = getContent('booking.content', true);
    $brands = \App\Models\Brand::active()->whereHas('vehicles')->orderBy('name')->get();
    $seaters = \App\Models\Seater::active()->whereHas('vehicles')->orderBy('number', 'asc')->get();
@endphp


<section class="book-section pt-120 pb-120 bg--section">
    <div class="container">
        <div class="book__wrapper  bg--section">
            <h4 class="book__title">{{ __($booking->data_values->heading) }}</h4>
            <form class="book--form row gx-3 gy-4 g-md-4" action="{{ route('vehicles.filter') }}" method="get">
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="car-type" class="form--label">
                            <i class="las la-car"></i> @lang('Select Brand')
                        </label>
                        <select name="brand_id" id="car-type" class="form-control form--control select2">
                            <option value="" disabled selected>@lang('Select One')</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ __(@$brand->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="pick-point" class="form--label">
                            <i class="las la-couch"></i> @lang('Number Of Seats')
                        </label>
                        <select name="seater_id" id="pick-point" class="form-control form--control select2">
                            <option value="" disabled selected>@lang('Select One')</option>
                            @foreach ($seaters as $seat)
                                <option value="{{ $seat->id }}">{{ __(@$seat->number) }} {{ __('Seater') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">
                            <i class="las la-tags"></i> @lang('Vehicle Model')
                        </label>
                        <input type="text" name="model" class="form-control form--control"
                               placeholder="@lang('Sedan, SUV ...')">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="start-datse" class="form--label">
                            <span class="text--base">{{ gs('cur_sym') }}</span> @lang('Min Price')
                        </label>
                        <input type="text" placeholder="@lang('Min Price')" name="min_price" id="start-datse"
                               autocomplete="off" class="form-control form--control">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">
                            <span class="text--base">{{ gs('cur_sym') }}</span> @lang('Max Price')
                        </label>
                        <input type="text" placeholder="@lang('Max Price')" name="max_price" autocomplete="off"
                               class="form-control form--control">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label d-none d-sm-block">&nbsp;</label>
                        <button class="cmn--btn form--control bg--base w-100 justify-content-center"
                                type="submit"> <i class="las la-filter"></i> @lang('Filter')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
