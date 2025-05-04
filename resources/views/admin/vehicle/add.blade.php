@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.vehicles.store') }}" method="post" enctype="multipart/form-data" class="disableSubmission">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Brand')</label>
                                    <select class="form-control select2" name="brand_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach (@$brands as $item)
                                            <option value="{{ $item->id }}">{{ __(@$item->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Seat Type')</label>
                                    <select class="form-control select2" name="seater_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach (@$seaters as $item)
                                            <option value="{{ $item->id }}">{{ __(@$item->number) }} @lang('Seater')</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Price Per Day')</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Details')</label>
                                    <textarea rows="10" name="details" class="form-control nicEdit">{{ old('details') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="image-wrapper">
                                    <div class="row gx-5 gy-4">
                                        <div class="col-lg-3 col-md-4 mb-3 ">
                                            <div class="form-group">
                                                <label>@lang('Image')</label>
                                                <x-image-uploader class="w-100" name="image[]" type="vehicle" image="" />
                                            </div>
                                            <button class="btn btn--danger remove-btn w-100" type="button" disabled><i class="las la-trash"></i> @lang('Remove')</button>
                                        </div>

                                        <div class="col-lg-3 col-md-4 vehicleImageHolder">
                                            <div class="add-new-card newVehicleImageHolder mt-3">
                                                <div class="add-new-card-box">
                                                    <i class="las la-plus-circle"></i>
                                                    <p>@lang('Add New')</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Model')</label>
                                    <input type="text" class="form-control" value="{{ old('model') }}"
                                           autocomplete="off" name="model" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Doors')</label>
                                    <input type="number"class="form-control" value="{{ old('doors') }}" name="doors" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Transmission')</label>
                                    <input type="text" class="form-control" value="{{ old('transmission') }}" name="transmission" required="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fuel">@lang('Fuel Type')</label>
                                    <input type="text" class="form-control" id="fuel" value="{{ old('fuel_type') }}"
                                           autocomplete="off" name="fuel_type" required="">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="card">
                                    <h5 class="card-header bg--primary d-flex justify-content-between">@lang('More Specifications')
                                        <button type="button" class="btn btn-sm btn-outline-light addBtn">
                                            <i class="las la-plus"></i>@lang('Add New')
                                        </button>
                                    </h5>

                                    <div class="card-body">
                                        <div class="row addedSpecification"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>

                        </div>
                    </div>

                </form>
            </div><!-- card end -->
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Specification')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body specification">
                    <div class="form-group">
                        <label>@lang('Select Icon')</label>
                        <div class="input-group">
                            <input type="text" class="form-control iconPicker icon" id="icon" autocomplete="off" name="icon[]" required>
                            <span class="input-group-text input-group-addon iconPicker" data-icon="las la-home" role="iconpicker"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>@lang('Label')</label>
                        <input class="form-control" id="label" type="text" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Value')</label>
                        <input class="form-control" id="value" type="text" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--primary addSpecification"> <i class="las la-plus"></i> @lang('Add')</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.vehicles.index') }}" />
@endpush

@push('style')
    <style>
        .image-wrapper {
            padding: 30px;
            border: 1px solid #00000012;
            border-radius: 4px;
            margin: 20px 0;
        }

        .add-new-card {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 90%;
            border: 1px solid #cacaca73;
            border-radius: 10px;
            cursor: pointer;
            padding: 30px;
        }

        .add-new-card-box {
            text-align: center;
            color: #b1b1b1;
        }

        .add-new-card-box i {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .add-new-card-box p {
            font-weight: 500;
        }

        .add-new-card:hover {
            background: #f3f3f3;
        }

        .add-new-card:hover .add-new-card-box {
            color: hsl(var(--body-color));
        }
    </style>
@endpush


@push('style-lib')
    <link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let cardCounter = 2;
            $('.newVehicleImageHolder').on('click', function() {
                let uniqueId = 'image-upload-input' + cardCounter;
                $(".vehicleImageHolder").before(`
                <div class="col-lg-3 col-md-4">
                    <div class="form-group">
                        <label >@lang('Image')<span class="text--danger">*</span></label>
                        <x-image-uploader image="" class="w-100" id="${uniqueId}" type="vehicle" name="image[]"/>
                    </div>
                    <button type="button" class="btn btn--danger remove-btn w-100"><i class="las la-trash"></i> @lang('Remove')</button>
            </div>
                `)
                cardCounter++;
                disableRemoveCard()
            });
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('div').remove();
                disableRemoveCard()
            });

            function disableRemoveCard() {
                if ($(document).find('.remove-btn').length == 1) {
                    $(document).find('.remove-btn').attr('disabled', true);
                } else {
                    $(document).find('.remove-btn').removeAttr('disabled');
                }
            }

            // Function to handle image preview
            function proPicURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var preview = $(input).closest('.image-upload-wrapper').find('.image-upload-preview');
                        preview.css('background-image', 'url(' + e.target.result + ')');
                        preview.addClass('has-image');
                        preview.hide();
                        preview.fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Event listener for image input change using event delegation
            $(document).on('change', '.image-upload-input', function() {
                proPicURL(this);
            });

            // Event listener for remove image button using event delegation
            $(document).on('click', '.remove-image', function() {
                var preview = $(this).closest('.image-upload-wrapper').find('.image-upload-preview');
                preview.css('background-image', 'none');
                preview.removeClass('has-image');
                $(this).closest('.image-upload-wrapper').find('input[type=file]').val('');
            });

            // Update file input field text using event delegation
            $(document).on('change', '.file-upload-field', function() {
                $(this).parent('.file-upload-wrapper').attr('data-text', $(this).val().replace(/.*(\/|\\)/, ''));
            });


            //----- Add Information fields-------//
            $('.addBtn').on('click', function() {
                var modal = $('#addModal');
                modal.modal('show');
            });


            const clearModal = () => {
                $('#icon').val('');
                $('#label').val('');
                $('#value').val('');
                $('#addModal').modal('hide');
            };

            // Function to reinitialize icon picker
            const reinitializeIconPicker = () => {
                $('.iconPicker').iconpicker();
            };

            // Initialize icon picker
            $('.iconPicker').iconpicker();

            $('.addSpecification').on('click', function() {
                var icon = $('#icon').val();
                var label = $('#label').val();
                var value = $('#value').val();

                console.log(icon, label, value);

                if (icon && label && value) {
                    var html = `
                    <div class="col-md-12 other-info-data">
                        <div class="form-group">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control iconPicker icon" autocomplete="off" name="icon[]" value='${icon}' required readonly>
                                        <span class="input-group-text iconPicker" data-icon="las la-home" role="iconpicker">${icon}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <input name="label[]" class="form-control" type="text" value="${label}" required readonly>
                                </div>
                                <div class="col-md-3 mt-2 mt-md-0">
                                    <input name="value[]" class="form-control" value="${value}" type="text" required readonly>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button class="btn btn-danger w-100 h-45 removeInfoBtn" type="button">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> `;

                    $('.addedSpecification').append(html);

                    //Clear ,  Reinitialize icon picker for newly added elements
                    clearModal();
                    reinitializeIconPicker();
                } else {
                    notify('error', 'Please fill out all fields')
                }
            });

            // Remove dynamically added specifications
            $(document).on('click', '.removeInfoBtn', function() {
                $(this).closest('.other-info-data').remove();
            });

            $('.iconPicker').iconpicker().on('iconpickerSelected', function(e) {
                $(this).closest('.form-group').find('.iconpicker-input').val(`<i class="${e.iconpickerValue}"></i>`);
            });

            $('select[name=brand]').val('{{ old('brand_id') }}');
            $('select[name=seater]').val('{{ old('seater_id') }}');

        })(jQuery);
    </script>
@endpush
