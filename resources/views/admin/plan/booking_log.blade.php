@extends('admin.layouts.app')
@section('panel')
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Plan')</th>
                                <th>@lang('Price | TRX')</th>
                                <th>@lang('Pick | Location')</th>
                                <th>@lang('Pick | Drop Time')</th>
                                <th>@lang('Status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($planLogs as $log)
                                <tr>
                                    <td>
                                       {{ $log->user->fullname }}
                                        <br>
                                        <small> <a href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ $log->user->username }}</a> </small>
                                    </td>

                                    <td>{{ __($log->plan->name) }}</td>
                                    <td>{{ showAmount($log->price) }}<br>
                                      {{ @$log->trx }}
                                    </td>
                                    <td>{{ __($log->pickUpLocation->name) }}</td>
                                    <td>
                                        {{ showDateTime($log->pick_time) }}<br>{{ showDateTime($log->drop_time) }}
                                    </td>
                                    <td>
                                        @if($log->pick_time > now())
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
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if($planLogs->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($planLogs) }}
                </div>
             @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
<x-search-form placeholder="Plan|trx"/>
@endpush
