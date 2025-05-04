@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="pb-60 pt-60">
        <div class="table-responsive">
            <table class="table cmn--table">
                <thead>
                    <tr>
                        <th>@lang('Plan')</th>
                        <th>@lang('Price | TRX')</th>
                        <th>@lang('Pick Location')</th>
                        <th>@lang('Pick | Drop Time')</th>
                        <th>@lang('Status')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookingLogs as $log)
                        <tr>
                            <td>{{ __($log->plan->name) }}</td>
                            <td>{{ showAmount($log->price) }}<br>
                                <strong>{{ @$log->trx }}</strong>
                            </td>
                            <td><i class="las la-map-marker"></i>{{ __($log->pickUpLocation->name) }}</td>
                            <td>
                                <span class="text--base">{{ showDateTime($log->pick_time) }}</span><br>
                                <strong class="text--warning">{{ showDateTime($log->drop_time) }}</strong>
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
                                        <button type="button" name="status" value="2"
                                            class="btn btn-sm status-btn 
                                            {{ $log->status == 2 ? 'btn-danger' : 'btn-outline-danger' }}">
                                            @lang('Canceled')
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="100%">
                                @include($activeTemplate . 'partials.empty', [
                                    'message' => 'Plan Booking Not Found!',
                                ])
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
                    url: `/user/update-status/${logId}`,
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
