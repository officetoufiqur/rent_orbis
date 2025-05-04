@php
    $appContent = getContent('app.content', true);
@endphp

<!-- Apps Section -->
<section class="apps-section pt-120 pb-120 position-relative overflow-hidden">
    <div class="shape">{{ __(@$appContent->data_values->stylish_text) }}</div>
    <div class="container position-relativ text-center text-lg-start">
        <div class="row gy-5 flex-wrap-reverse justify-content-between align-items-center">
            <div class="col-lg-5 col-xl-4">
                <div class="app-thumb">
                    <img src="{{ frontendImage('app', @$appContent->data_values->image, '1042x2114') }}" alt="app">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section__header">
                    <span class="section__category">{{ __(@$appContent->data_values->heading) }}</span>
                    <h2 class="section__title">{{ __(@$appContent->data_values->subheading) }}</h2>
                </div>
                <div class="mt-5">
                    <p>{{ __(@$appContent->data_values->content) }}</p>
                    <div class="btn__grp mt-5  justify-content-center justify-content-lg-start">
                        <a href="{{ @$appContent->data_values->app_store_link }}">
                            <img src="{{ asset($activeTemplateTrue . 'images/app/app-store-btn.svg') }}" alt="app">
                        </a>
                        <a href="{{ @$appContent->data_values->google_play_link }}">
                            <img src="{{ asset($activeTemplateTrue . 'images/app/google-play.svg') }}" alt="app">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Apps Section -->
