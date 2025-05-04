@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="pt-60 pb-60">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card custom--card">
                    <div class="card-body p-5">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You have to pay '):
                                <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You will get '):
                                <strong>{{ showAmount($deposit->amount) }}</strong>
                            </li>
                        </ul>
                        <form action="{{ $data->url }}" method="{{ $data->method }}">
                            <input type="hidden" custom="{{ $data->custom }}" name="hidden">
                            <script src="{{ $data->checkout_js }}"
                                    @foreach ($data->val as $key => $value)
                                    data-{{ $key }}="{{ $value }}" @endforeach></script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $('input[type="submit"]').addClass("mt-4 btn cmn--btn w-100");
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .list-group-item {
            color: var(--bs-list-group-bg);
            background-color: transparent;
        }
    </style>
@endpush
