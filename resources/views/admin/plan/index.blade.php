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
                                <th>@lang('Name')</th>
                                <th>@lang('Price Per Ride')</th>
                                <th>@lang('Number Of Days')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($plans as $item)
                                <tr>
                                    <td>{{ __($item->name) }}</td>
                                    <td>{{ showAmount($item->price) }}</td>
                                    <td>{{ $item->days }}</td>
                                    <td> @php echo $item->statusBadge; @endphp </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.plans.edit', $item->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </a>
                                            @if ($item->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-action="{{ route('admin.plans.status', $item->id) }}" data-question="@lang('Are you sure to enable this seater')?" type="button">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.plans.status', $item->id) }}" data-question="@lang('Are you sure to disable this seater')?" type="button">
                                                    <i class="la la-eye-slash"></i> @lang('Disable')
                                                </button>
                                            @endif
                                        </div>
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
                @if($plans->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($plans) }}
                </div>
             @endif
            </div><!-- card end -->
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="name|price"/>
    <a href="{{ route('admin.plans.add') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush
