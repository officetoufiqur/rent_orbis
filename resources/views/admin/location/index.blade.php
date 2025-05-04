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
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse ($locations as $location)
                                <tr>
                                    <td>{{ $loop->index + $locations->firstItem() }}</td>
                                    <td>{{ __($location->name) }}</td>
                                    <td>  @php echo $location->statusBadge;  @endphp </td>
                                    <td>
                                        <div class="button--group">
                                            <button class="btn btn-sm btn-outline--primary editBtn cuModalBtn" data-resource="{{ $location }}" data-modal_title="@lang('Edit location')" data-has_status="1" type="button">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </button>
                                            @if ($location->status == Status::DISABLE)
                                                <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-action="{{ route('admin.location.status', $location->id) }}" data-question="@lang('Are you sure to enable this location')?" type="button">
                                                    <i class="la la-eye"></i> @lang('Enable')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.location.status', $location->id) }}" data-question="@lang('Are you sure to disable this location')?" type="button">
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
                @if($locations->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($locations) }}
                    </div>
                 @endif
            </div><!-- card end -->
        </div>
    </div>

     <!--Cu Modal -->
     <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.location.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />

    <button class="btn btn-sm btn-outline--primary cuModalBtn" type="button" data-modal_title="@lang('Add location')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush
