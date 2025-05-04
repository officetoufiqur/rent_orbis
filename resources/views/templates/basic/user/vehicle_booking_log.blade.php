@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="pb-60 pt-60">
        <div class="table-responsive">
            <table class="table cmn--table">
                <thead>
                    <tr>
                        <th>@lang('Vehicle')</th>
                        <th>@lang('Price | TRX')</th>
                        <th>@lang('Pick | Drop Location')</th>
                        <th>@lang('Pick | Drop Time')</th>
                        <th>@lang('Status')</th>
                    </tr>
                </thead>
                <tbody>
                        @forelse ($bookingLogs as $log)
                            <tr>
                                <td>
                                    <div>{{ __($log->vehicle->name) }}</div>
                                </td>
                                <td>
                                    <div>
                                        {{ showAmount($log->price) }}<br> <strong>{{ @$log->trx }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <i class="las la-map-marker-alt"></i>{{ __(@$log->pickUpLocation->name) }} <br>
                                        <i class="las la-map-marker"></i><strong>{{ __(@$log->dropUpLocation->name) }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="text--base">{{ showDateTime($log->pick_time) }}</span><br><strong class="text--warning">{{ showDateTime($log->drop_time) }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <form class="status-form text-light" data-id="{{ $log->id }}">
                                        @csrf
                                        <div class="btn-group">
                                            <button type="button" name="status" value="0"
                                                class="btn btn-sm status-btn 
                                                {{ $log->status == 0 ? 'btn-warning' : 'btn-outline-warning' }}">
                                                @lang('Running')
                                            </button>
                                            <button type="button" name="status" value="1"
                                                class="btn btn-sm status-btn 
                                                {{ $log->status == 1 ? 'btn-success' : 'btn-outline-success' }}">
                                                @lang('Completed')
                                            </button>
                                            {{-- <span>{{ showDateTime($log->pick_time) }}</span>
                                            <button type="button" name="status" value="2"
                                                class="btn btn-sm status-btn 
                                                {{ $log->status == 2 ? 'btn-danger' : 'btn-outline-danger' }}">
                                                @lang('Canceled')
                                            </button> --}}
    
                                            @if ($log->pick_time <= now()->addHours(12))
                                                <button type="button" name="status" value="2"
                                                    class="btn btn-sm status-btn d-none {{ $log->status == 2 ? 'btn-danger' : 'btn-outline-danger' }}">
                                                    @lang('Canceled')
                                                </button>
                                            @else
                                                <button type="button" name="status" value="2"
                                                    class="btn btn-sm status-btn 
                                                    {{ $log->status == 2 ? 'btn-danger' : 'btn-outline-danger' }}">
                                                    @lang('Canceled')
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">
                                    @include($activeTemplate . 'partials.empty', ['message' => 'Vehicle Booking Not Found!'])
                                </td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookingLogs->hasPages())
            {{ paginateLinks($bookingLogs) }}
        @endif

    </div>
@endsection
@push('script')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        $(document).ready(function() {
            $('.status-btn').click(function() {
                const form = $(this).closest('.status-form');
                const logId = form.data('id');
                const status = $(this).val();

                $.ajax({
                    url: `/vehicle-booking-log/${logId}`,
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        status: status
                    },
                    success: function(data) {
                        if (data.success) {
                            form.find('.status-btn').each(function() {
                                $(this)
                                    .removeClass('btn-warning btn-success btn-danger')
                                    .addClass(
                                        'btn-outline-warning btn-outline-success btn-outline-danger'
                                    );

                                if ($(this).val() == status) {
                                    $(this).removeClass(
                                            `btn-outline-${getStatusClass(status)}`)
                                        .addClass(`btn-${getStatusClass(status)}`);
                                }
                            });

                            Toastify({
                                text: "Status updated successfully",
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                            }).showToast();
                        }
                    }
                });
            });

            function getStatusClass(status) {
                switch (status) {
                    case '0':
                        return 'warning';
                    case '1':
                        return 'success';
                    case '2':
                        return 'danger';
                    default:
                        return 'secondary';
                }
            }
        });
    </script>
@endpush