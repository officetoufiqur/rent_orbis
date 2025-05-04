@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')

    @include($activeTemplate . 'partials.breadcrumb')

    <main class="dashboard-section bg--section pt-60 pb-60">
        <div class="container">
    
            @yield('content')
    
        </div>
    </main>

    @include($activeTemplate . 'partials.footer')
@endsection
