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
                                <span class="text--base">{{ showDateTime($log->pick_time) }}</span><br><strong class="text--warning">{{ showDateTime($log->drop_time) }}</strong>
                            </td>

                            <td>
                                @if ($log->pick_time > now())
                                    <span class="badge badge--primary">@lang('Upcoming')</span>
                                @elseif($log->pick_time < now() && $log->drop_time > now())
                                    <span class="badge badge--warning">@lang('Running')</span>
                                @elseif($log->drop_time < now())
                                    <span class="badge badge--success">@lang('Completed')</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="100%">
                                @include($activeTemplate . 'partials.empty', ['message' => 'Plan Booking Not Found!'])
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
