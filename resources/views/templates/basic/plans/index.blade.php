@extends($activeTemplate.'layouts.frontend')

@section('content')
    <div class="faq-section pt-120 pb-120 bg--section position-relative overflow-hidden">
        <div class="shape">@lang('pricing')</div>
        <div class="shape right-side">@lang('plan')</div>
        <div class="container">
            <div class="row g-4 justify-content-center">
                @include($activeTemplate . 'partials.plan')
            </div>
        </div>
    </div>

    @if(@$sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif

@endsection
