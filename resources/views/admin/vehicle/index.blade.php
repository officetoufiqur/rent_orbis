@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Brand')</th>
                                    <th>@lang('Seat Type')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Model')</th>
                                    <th>@lang('Transmission')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($vehicles as $vehicle)
                                <tr>
                                    <td>{{ __($vehicle->name) }}</td>
                                    <td>{{ __($vehicle->brand->name) }}</td>
                                    <td>{{ __($vehicle->seater->number) }} @lang('Seater')</td>
                                    <td>{{ showAmount($vehicle->price) }}</strong></td>
                                    <td>{{ __($vehicle->model) }}</td>
                                    <td>{{ __($vehicle->transmission) }}</td>
                                    <td>  @php echo $vehicle->statusBadge;  @endphp </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </a>
                                            @if ($vehicle->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-action="{{ route('admin.vehicles.status', $vehicle->id) }}" data-question="@lang('Are you sure to enable this vehicle')?" type="button">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.vehicles.status', $vehicle->id) }}" data-question="@lang('Are you sure to disable this vehicle')?" type="button">
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
                @if($vehicles->haspages())
                <div class="card-footer">
                    {{ paginateLinks($vehicles) }}
                </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Name/brand/model" />
    <a href="{{ route('admin.vehicles.add') }}" class="btn btn-outline--primary"><i class="las la-plus"></i>@lang('Add New')</a>
@endpush

