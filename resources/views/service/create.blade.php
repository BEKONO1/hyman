<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            <a href="{{ url()->previous() === route('service.provider-service-request') ? route('service.provider-service-request') : route('service.index') }}"
                                class="float-end btn btn-sm btn-primary">
                                <i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}
                            </a>
                            @if ($auth_user->can('service list'))
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('service.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('service')->open() }}
                        {{ html()->hidden('id', $servicedata->id ?? null) }}

                        @include('partials._language_toggale')
                        @foreach($language_array as $language)
                        <div id="form-language-{{ $language['id'] }}" class="language-form" style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
                            <div class="row">
                                @foreach(['name' => __('messages.name'), 'description' => __('messages.description')] as $field => $label)
                                <div class="form-group col-md-{{ $field === 'name' ? '4' : '12' }}">
                                    {{ html()->label($label . ($field === 'name' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}
                                    @php
                                        $value = $language['id'] == 'en' 
                                            ? $servicedata ? $servicedata->translate($field, 'en') : '' 
                                            : ($servicedata ? $servicedata->translate($field, $language['id']) : '');
                                        $name = $language['id'] == 'en' ? $field : "translations[{$language['id']}][$field]";
                                    @endphp

                            @if($field === 'name')
    {{ html()->text($name, $value)
        ->placeholder($label)
        ->class('form-control')
        ->attribute('title', 'Please enter alphabetic characters and spaces only')
        ->attribute('data-required', 'true') }}
@elseif($field === 'description')
    {{ html()->textarea($name, $value)
        ->class('form-control textarea description-field')
        ->attribute('maxlength', 250)
        ->rows(3)
        ->placeholder($label)
        ->attribute('data-lang', $language['id']) }}

    <small class="text-muted">
        <span class="char-count" id="char-count-{{ $language['id'] }}">{{ strlen($value ?? '') }}</span>/250
    </small>
@endif

                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                                @endforeach
         
                                <!-- Category Selection -->
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'category_id')->class('form-control-label') }}
                                    <select name="category_id"
                                            id="category_id_{{ $language['id'] }}"
                                            class="form-select select2js-category"
                                            data-select2-type="category"
                                            data-selected-id="{{ $servicedata->category_id ?? '' }}"
                                            data-language-id="{{ $language['id'] }}"
                                            data-ajax--url="{{ route('ajax-list', ['type' => 'category', 'language_id' => $language['id']]) }}"
                                            data-placeholder="{{ __('messages.select_name', ['select' => __('messages.category')]) }}">
                                        </select>
                                        <small class="help-block with-errors text-danger"></small>
                                    </div>

                                    <!-- SubCategory Selection -->
                                    <div class="form-group col-md-4">
                                        {{ html()->label(__('messages.select_name', ['select' => __('messages.subcategory')]), 'category_id')->class('form-control-label') }}
                                        <select name="subcategory_id" id="subcategory_id_{{ $language['id'] }}"
                                            class="form-select select2js-subcategory subcategory_id"
                                            data-select2-type="subcategory"
                                            data-selected-id="{{ $servicedata->subcategory_id ?? '' }}"
                                            data-language-id="{{ $language['id'] }}"
                                            data-ajax--url="{{ route('ajax-list', ['type' => 'subcategory', 'category_id' => $servicedata->category_id ?? '', 'language_id' => $language['id']]) }}"
                                            data-placeholder="{{ __('messages.select_name', ['select' => __('messages.subcategory')]) }}">
                                        </select>
                                        <small class="help-block with-errors text-danger"></small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="row">
                            <!-- <div class="form-group col-md-4">
                                {{ html()->label(__('messages.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                {{ html()->text('name', $servicedata->name)->placeholder(__('messages.name'))->class('form-control')->attributes(['title' => 'Please enter alphabetic characters and spaces only']) }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                <br />
                                {{ html()->select(
                                        'category_id',
                                        [optional($servicedata->category)->id => optional($servicedata->category)->name],
                                        optional($servicedata->category)->id,
                                    )->class('select2js form-group category')->required()->id('category_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.category')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'category'])) }}

                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.subcategory')]), 'subcategory_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('subcategory_id', [])->class('select2js form-group subcategory_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.subcategory')])) }}
                            </div> -->

                            @if (auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.select_name', ['select' => __('messages.provider')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                    <br />
                                    {{ html()->select(
                                            'provider_id',
                                            [optional($servicedata->providers)->id => optional($servicedata->providers)->display_name],
                                            optional($servicedata->providers)->id,
                                        )->class('select2js form-group')->id('provider_id')->attribute('onchange', 'selectprovider(this)')->required()->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.provider')]))->attribute('data-ajax--url', route('ajax-list', ['type' => 'provider'])) }}
                                </div>
                            @endif
                            @if (auth()->user()->hasRole('provider'))
                                <input type="hidden" id="provider_id" value="{{ auth()->id() }}">
                            @endif

                            <!-- Zone Selection -->
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.zone')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                <br />
                                {{ html()->select('service_zones[]', [], old('service_zones', $selectedZones ?? []))->class('select2js form-group zone_id')->id('service_zones')->multiple()->required()->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.zone')])) }}
                            </div>



                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.price_type') . ' <span class="text-danger">*</span>', 'type')->class('form-control-label') }}
                                {{ html()->select('type', ['fixed' => __('messages.fixed'), 'hourly' => __('messages.hourly'), 'free' => __('messages.free')], $servicedata->type)->class('form-select select2js')->required()->id('price_type') }}
                            </div>
                            <div class="form-group col-md-4" id="price_div">
                                {{ html()->label(__('messages.price') . ' <span class="text-danger">*</span>', 'price')->class('form-control-label') }}
                                {{ html()->text('price', null)->attributes(['min' => 1, 'step' => 'any', 'pattern' => '^\\d+(\\.\\d{1,2})?$'])->placeholder(__('messages.price'))->class('form-control')->required()->id('price') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>

                            <div class="form-group col-md-4" id="discount_div">
                                {{ html()->label(__('messages.discount') . ' %', 'discount')->class('form-control-label') }}
                                {{ html()->number('discount', null)->attributes(['min' => 0, 'max' => 99, 'step' => 'any'])->placeholder(__('messages.discount'))->class('form-control')->id('discount') }}

                                <span id="discount-error" class="text-danger"></span>
                            </div>


                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.duration') . ' (hours) ', 'duration')->class('form-control-label') }}
                                {{ html()->text('duration', $servicedata->duration)->placeholder(__('messages.duration'))->class('form-control min-datetimepicker-time') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $servicedata->status)->class('form-select select2js')->required() }}
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.visit_type') . ' ', 'visit_type')->class('form-control-label') }}
                                <br />
                                {{ html()->select('visit_type', $visittype, $servicedata->visit_type)->id('visit_type')->class('form-select select2js')->required() }}
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-control-label" for="service_attachment">{{ __('messages.image') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="custom-file">
                                    <input type="file" onchange="preview()" name="service_attachment[]"
                                        class="custom-file-input"
                                        data-file-error="{{ __('messages.files_not_allowed') }}" multiple
                                        accept="image/*" required>
                                    <label
                                        class="custom-file-label upload-label">{{ __('messages.choose_file', ['file' => __('messages.attachments')]) }}</label>
                                </div>
                                <img id="service_attachment_preview"
                                    style="margin-top: 10px; max-width: 100%; display: none;" alt="Preview">
                            </div>
                        </div>

                        <!-- SEO Enable/Disable Switch -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <!-- @php
                                            $seoEnabled = !empty($servicedata->meta_title)
                                                || !empty($servicedata->meta_description)
                                                || !empty($servicedata->meta_keywords)
                                                || !empty($servicedata->slug)
                                        @endphp -->
                                        {{ html()->checkbox('seo_enabled', $servicedata->seo_enabled)->class('custom-control-input')->id('seo_enabled') }}
                                        <label class="custom-control-label" for="seo_enabled">{{ __('messages.set_seo') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Fields Section for this language -->
                        <div class="row mt-4" id="seo_fields_section">
                            <div class="col-12">
                                <h5 class="fw-bold mb-3">{{ __('messages.seo_fields') }}</h5>
                            </div>
                            <!-- First Row: SEO Image (left) and Meta Title (right) -->
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    {{ html()->label(__('messages.seo_image'), 'seo_image')->class('form-control-label language-label') }}
                                    @php
                                        $seoImageUrl = (isset($servicedata->id) && getMediaFileExit($servicedata, 'seo_image')) ? $servicedata->getFirstMediaUrl('seo_image') : '';
                                        $seoImageHas = !empty($seoImageUrl) ? '1' : '0';
                                    @endphp 
                                    <input type="file" name="seo_image" class="form-control" accept=".jpg,.jpeg,.png" placeholder="{{ __('messages.choose_file', ['file' => __('messages.image')]) }}" onchange="previewSeoImage(event)" data-has-image="{{ $seoImageHas }}">
                                    <small class="help-block with-errors text-danger"></small>
                                    <small class="text-muted d-block mt-1">{{ __('messages.only_jpg_png_jpeg_allowed') }}</small> 
                                    <!-- @php
                                        $seoImageUrl = (isset($servicedata->id) && getMediaFileExit($servicedata, 'seo_image')) ? $servicedata->getFirstMediaUrl('seo_image') : '';
                                    @endphp -->
                                    <img id="seo_image_preview" src="{{ $seoImageUrl }}" alt="SEO Image Preview" style="max-width: 100px; margin-top: 10px; @if(empty($seoImageUrl)) display: none; @endif" />
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{ html()->label(__('messages.meta_title'), 'meta_title')->class('form-control-label language-label') }}
                                        <span class="text-muted" style="font-size: 12px;">
                                            <span id="meta-title-count">{{ strlen($servicedata->meta_title ?? '') }}</span>/100
                                        </span>
                                    </div>
                                    @php
                                        $metaTitleValue = isset($servicedata->id) ? $servicedata->meta_title : '';
                                    @endphp
                                    {{ html()->text('meta_title', $metaTitleValue)
                                        ->placeholder(__('messages.enter_meta_title'))
                                        ->class('form-control')
                                        ->attribute('maxlength', 100)
                                        ->attribute('id', 'meta_title') }}
                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                            </div>
                            <!-- Second Row: Meta Keywords (half width, with symmetry) -->
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{ html()->label(__('messages.meta_keywords'), 'meta_keywords')->class('form-control-label language-label') }}
                                    </div>
                                    @php
                                        $metaKeywordsValue = isset($servicedata->id) ? (is_array($servicedata->meta_keywords) ? implode(',', $servicedata->meta_keywords) : $servicedata->meta_keywords) : '';
                                    @endphp
                                    <input id="meta_keywords" name="meta_keywords" value="{{ $metaKeywordsValue }}" placeholder="{{ __('messages.type_a_keyword_and_press_enter') }}" class="w-100" />
                                    <br />
                                    <small class="text-muted">{{ __('messages.type_a_keyword_and_press_enter') }}</small>
                                </div>
                                <div class="col-md-6 d-none"></div>
                            </div>
                            <!-- Third Row: Meta Description (full width) -->
                            <div class="form-group col-12 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    {{ html()->label(__('messages.meta_description'), 'meta_description')->class('form-control-label language-label') }}
                                    <span class="text-muted" style="font-size: 12px;">
                                        <span id="meta-desc-count">{{ strlen($servicedata->meta_description ?? '') }}</span>/200
                                    </span>
                                </div>
                                @php
                                    $metaDescValue = isset($servicedata->id) ? $servicedata->meta_description : '';
                                @endphp
                                {{ html()->textarea('meta_description', $metaDescValue)
                                    ->placeholder(__('messages.enter_meta_description'))
                                    ->class('form-control flex-grow-1')
                                    ->style('min-height: 120px; resize: vertical;')
                                    ->rows(4)
                                    ->attribute('maxlength', 200)
                                    ->attribute('id', 'meta_description') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="form-group col-md-12">
                                    {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
                                    {{ html()->textarea('description', $servicedata->description)->class('form-control textarea')->rows(3)->placeholder(__('messages.description')) }}
                                </div> -->
                            @if (!empty($slotservice) && $slotservice == 1)
                                <div class="form-group col-md-3">
                                    <div class="custom-control custom-switch">
                                        {{ html()->checkbox('is_slot', $servicedata->is_slot)->class('custom-control-input')->id('is_slot') }}
                                        <label class="custom-control-label"
                                            for="is_slot">{{ __('messages.slot') }}</label>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group col-md-3">
                                <div class="custom-control custom-switch">
                                    {{ html()->checkbox('is_featured', $servicedata->is_featured)->class('custom-control-input')->id('is_featured') }}
                                    <label class="custom-control-label"
                                        for="is_featured">{{ __('messages.set_as_featured') }}</label>
                                </div>
                            </div>
                            <!-- @if (!empty($digitalservicedata) && $digitalservicedata->value == 1)
<div class="form-group col-md-3">
                                <div class="custom-control custom-switch">
                                    {{ Form::checkbox('digital_service', $servicedata->digital_service, null, ['class' => 'custom-control-input', 'id' => 'digital_service']) }}
                                    <label class="custom-control-label"
                                        for="digital_service">{{ __('messages.digital_service') }}</label>
                                </div>
                            </div>
@endif -->
                            @if (!empty($advancedPaymentSetting) && $advancedPaymentSetting == 1)
                                <div class="form-group col-md-3" id="is_enable_advance">
                                    <div class="custom-control custom-switch">
                                        {{ html()->checkbox('is_enable_advance_payment', $servicedata->is_enable_advance_payment)->class('custom-control-input')->id('is_enable_advance_payment') }}
                                        <label class="custom-control-label"
                                            for="is_enable_advance_payment">{{ __('messages.enable_advanced_payment') }}
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group col-md-4" id="amount">
                                {{ html()->label(__('messages.advance_payment_amount') . ' <span class="text-danger">*</span> (%)', 'advance_payment_amount')->class('form-control-label') }}
                                {{ html()->number('advance_payment_amount', $servicedata->advance_payment_amount)->placeholder(__('messages.amount'))->class('form-control')->id('advance_payment_amount')->attributes(['min' => 1, 'max' => 99]) }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            @if (isset($servicedata->service_request_status) &&
                                    $servicedata->service_request_status == 'reject' &&
                                    !empty($servicedata->reject_reason))
                                <div class="form-group col-md-12 d-flex align-items-center">
                                    <label class="form-control-label mb-0 me-2 text-danger" for="reason">
                                        {{ __('messages.reason') }}:
                                    </label>
                                    <span>{{ $servicedata->reject_reason }}</span>
                                </div>
                            @endif
                        </div>


                        @if (auth()->user()->hasAnyRole(['admin', 'demo_admin']) &&
                                isset($servicedata) &&
                                $servicedata->is_service_request == 1 &&
                                (is_null($servicedata->service_request_status) || $servicedata->service_request_status == 'pending'))
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-light text-dark float-end"
                                    onclick="showRejectionConfirmation('{{ $servicedata->id }}', 'rejected')">Reject</button>
                                <button type="button" class="btn btn-sm btn-primary float-end me-3"
                                    onclick="showApprovalConfirmation('{{ $servicedata->id }}', 'approved')">Approve</button>
                            </div>
                        @elseif(auth()->user()->hasAnyRole(['admin', 'demo_admin']) &&
                                isset($servicedata->is_service_request) &&
                                ($servicedata->is_service_request == 1 || is_null($servicedata->is_service_request)) &&
                                $servicedata->service_request_status == 'reject')
                        @else
                            {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        @endif
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $data = $servicedata->providerServiceAddress->pluck('provider_address_id')->implode(',');
    @endphp
    @section('bottom_script')
        <script type="text/javascript">
            function preview() {
                var fileInput = event.target;
                var previewElement = document.getElementById('service_attachment_preview');
                if (fileInput.files && fileInput.files[0]) {
                    previewElement.src = URL.createObjectURL(fileInput.files[0]);
                    previewElement.style.display = 'block';
                } else {
                    previewElement.style.display = 'none';
                }
            }
            function previewSeoImage(event) {
                const preview = document.getElementById('seo_image_preview');
                const file = event.target.files[0];
                if (preview && file) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                }
            }
            var discountInput = document.getElementById('discount');
            var discountError = document.getElementById('discount-error');


            document.addEventListener('DOMContentLoaded', function() {
                if (typeof renderedDataTable === 'undefined') {
                    renderedDataTable = $('#datatable').DataTable();
                }

                var initialProviderId = document.getElementById('provider_id').value;
                selectprovider({
                    value: initialProviderId
                });

                  const textareas = document.querySelectorAll('.description-field');

        textareas.forEach(function (textarea) {
            textarea.addEventListener('input', function () {
                const langId = textarea.getAttribute('data-lang');
                const countSpan = document.getElementById('char-count-' + langId);

                if (countSpan) {
                    countSpan.textContent = textarea.value.length;
                }
            });
        });


                 const addLink = document.getElementById('add_provider_address_link');
    
    if (addLink) {
        addLink.addEventListener('click', function(event) {
            event.preventDefault();

            const providerId = document.getElementById('provider_id').value;
            let providerAddressCreateUrl = "{{ route('provideraddress.create', ['provideraddress' => '']) }}";
            
            providerAddressCreateUrl = providerAddressCreateUrl.replace('provideraddress=',
                'provideraddress=' + providerId);

            window.location.href = providerAddressCreateUrl;
        });
    }
                 
            });

            function updateServiceStatus(serviceId, status, reason = '') {
                $.ajax({
                    url: '{{ route('service.updateStatus') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: serviceId,
                        status: status,
                        reason: reason
                    },
                    success: function(response) {
                        if (response.success) {
                            if (status === 'approved') {
                                window.location.href = '{{ route('service.provider-service-request') }}';
                            } else {
                                var badge = '<span class="badge badge-danger">Rejected</span>';
                                var row = $('#datatable-row-' + serviceId);
                                row.find('.service-status').html(badge);
                                window.location.href = '{{ route('service.provider-service-request') }}';
                                renderedDataTable.ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while updating the status.',
                                icon: 'error',
                                confirmButtonText: 'Try Again'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while processing the request.',
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        });
                    }
                });
            }

            function showApprovalConfirmation(serviceId, status) {
                Swal.fire({
                    icon: 'success',
                    title: '',
                    html: '<span style="color: #333; font-weight: 550; font-size: 20px;">' +
                        '{{ __('messages.are_you_sure_you_want_to') }} ' +
                        (status === "approved" ?
                            '{{ __('messages.approve_this_service_into_list') }}' :
                            '{{ __('messages.reject_this_service_into_list') }}') +
                        '</span>',
                    showCancelButton: true,
                    cancelButtonText: '<span style="color: black; font-weight: 500;">{{ __('messages.cancel') }}</span>', // Black text, medium weight
                    confirmButtonText: '{{ __('messages.approve') }}',
                    confirmButtonColor: '#6366F1',
                    cancelButtonColor: '#E5E7EB',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateServiceStatus(serviceId, status);
                    }
                });
            }

            function showRejectionConfirmation(serviceId) {
                Swal.fire({
                    title: `<h2 style="font-size: 20px; font-weight: bold; margin-bottom: 15px;">{{ __('messages.reject_service_confirmation_title') }}</h2>`,
                    text: '{{ __('messages.provide_rejection_reason') }}',
                    html: `
            <div style="text-align: left; margin-top: 5px; background-color: #f0f0f0; padding: 20px; border-radius: 10px;">
                <label for="reject-reason" style="font-size: 14px; font-weight: bold; display: block; margin-bottom: 5px;">
                    Provide the reason for rejection
                </label>
                <textarea id="reject-reason" placeholder="e.g. Insufficient details" 
                    style="width: 100%; height: 100px; background-color: #ffffff; border: 1px solid #ccc; 
                    border-radius: 8px; padding: 10px; font-size: 14px; resize: none;"></textarea>
            </div>
        `,
                    icon: 'error',
                    inputAttributes: {
                        'aria-label': '{{ __('messages.rejection_reason_aria') }}'
                    },
                    showCancelButton: true,
                    confirmButtonText: '<span style="font-size: 14px; font-weight: bold;">{{ __('messages.reject') }}</span>',
                    cancelButtonText: '<span style="font-size: 14px; font-weight: bold; color: black;">{{ __('messages.cancel') }}</span>',
                    cancelButtonColor: '#f0f0f0',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        var rejectionReason = document.getElementById('reject-reason').value;
                        if (rejectionReason.trim() !== "") {
                            updateServiceStatus(serviceId, 'rejected', rejectionReason);
                        } else {
                            Swal.fire({
                                title: '{{ __('messages.error') }}',
                                text: '{{ __('messages.rejection_reason_required') }}',
                                icon: 'error',
                                confirmButtonText: '{{ __('messages.okay') }}'
                            });
                        }
                    }
                });

            }

            function selectprovider(selectElement) {
                var providerId = selectElement.value;
                var zoneDropdown = $('#service_zones');

                if (providerId) {
                    // Load zones for the selected provider
                    $.ajax({
                        url: "{{ route('ajax-list', ['type' => 'zone']) }}",
                        data: {
                            provider_id: providerId
                        },
                        success: function(result) {
                            // Clear existing options
                            zoneDropdown.empty();

                            // Add new options from the response
                            if (result.results && result.results.length > 0) {
                                $.each(result.results, function(index, item) {
                                    var option = new Option(item.text, item.id, false, false);
                                    zoneDropdown.append(option);
                                });
                            }

                            // Initialize Select2
                            zoneDropdown.select2({
                                width: '100%',
                                placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.zone')]) }}",
                                allowClear: true
                            });

                            // If we have selected zones from editing, set them
                            @if (isset($selectedZones) && !empty($selectedZones))
                                var selectedZones = @json($selectedZones);
                                if (selectedZones && selectedZones.length > 0) {
                                    zoneDropdown.val(selectedZones).trigger('change');
                                }
                            @endif
                        }
                    });
                } else {
                    zoneDropdown.empty().trigger('change');
                }
            }

            // Initialize Select2 for service zones on page load
            $(document).ready(function() {
                // Initialize the zone dropdown
                $('#service_zones').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.zone')]) }}"
                });

                // Initialize provider dropdown with Select2
                $('#provider_id').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.provider')]) }}",
                    allowClear: true
                });

                // Always call selectprovider on load
                var initialProviderId = $('#provider_id').val();
                if (initialProviderId) {
                    selectprovider({
                        value: initialProviderId
                    });
                }
            });

            discountInput.addEventListener('input', function() {
                var discountValue = parseFloat(discountInput.value);
                if (isNaN(discountValue) || discountValue < 0 || discountValue > 99) {
                    discountError.textContent = "{{ __('Discount value should be between 0 to 99') }}";
                } else {
                    discountError.textContent = "";
                }
            });

            var isEnableAdvancePayment = $("input[name='is_enable_advance_payment']").prop('checked');

            var priceType = $("#price_type").val();

            enableAdvancePayment(priceType);
            checkEnablePayment(isEnableAdvancePayment);

            $("#is_enable_advance_payment").change(function() {
                isEnableAdvancePayment = $(this).prop('checked');
                checkEnablePayment(isEnableAdvancePayment);
                updateAmountVisibility(priceType, isEnableAdvancePayment);
            });

            $("#price_type").change(function() {
                priceType = $(this).val();
                enableAdvancePayment(priceType);
                updateAmountVisibility(priceType, isEnableAdvancePayment);
            });

            function checkEnablePayment(value) {
                $("#amount").toggleClass('d-none', !value);
                $('#advance_payment_amount').prop('required', false);
            }

            function enableAdvancePayment(type) {
                $("#is_enable_advance").toggleClass('d-none', type !== 'fixed');
            }

            function updateAmountVisibility(type, isEnableAdvancePayment) {
                if (type === 'fixed' && !$("#is_enable_advance").hasClass('d-none') && isEnableAdvancePayment) {
                    $("#amount").removeClass('d-none');
                } else {
                    $("#amount").addClass('d-none');
                }
            }

            (function($) {
                "use strict";
                $(document).ready(function() {
                    var provider_id = "{{ isset($servicedata->provider_id) ? $servicedata->provider_id : '' }}";
                    var provider_address_id = "{{ isset($data) ? $data : [] }}";

                    var category_id = "{{ isset($servicedata->category_id) ? $servicedata->category_id : '' }}";
                    var subcategory_id =
                        "{{ isset($servicedata->subcategory_id) ? $servicedata->subcategory_id : '' }}";

                    var price_type = "{{ isset($servicedata->type) ? $servicedata->type : '' }}";

                    providerAddress(provider_id, provider_address_id)
                    getSubCategory(category_id, subcategory_id)
                    priceformat(price_type)

                    $(document).on('change', '#provider_id', function() {
                        var provider_id = $(this).val();
                        $('#provider_address_id').empty();
                        providerAddress(provider_id, provider_address_id);
                    })
                    $(document).on('change', '#category_id', function() {
                        var category_id = $(this).val();
                        $('#subcategory_id').empty();
                        getSubCategory(category_id, subcategory_id);
                    })
                    $(document).on('change', '#price_type', function() {
                        var price_type = $(this).val();
                        priceformat(price_type);
                    })


                    $('.galary').each(function(index, value) {
                        let galleryClass = $(value).attr('data-gallery');
                        $(galleryClass).magnificPopup({
                            delegate: 'a#attachment_files',
                            type: 'image',
                            gallery: {
                                enabled: true,
                                navigateByImgClick: true,
                                preload: [0,
                                    1
                                ] // Will preload 0 - before current, and 1 after the current image
                            },
                            callbacks: {
                                elementParse: function(item) {
                                    if (item.el[0].className.includes('video')) {
                                        item.type = 'iframe',
                                            item.iframe = {
                                                markup: '<div class="mfp-iframe-scaler">' +
                                                    '<div class="mfp-close"></div>' +
                                                    '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                                                    '<div class="mfp-title">Some caption</div>' +
                                                    '</div>'
                                            }
                                    } else {
                                        item.type = 'image',
                                            item.tLoading = 'Loading image #%curr%...',
                                            item.mainClass = 'mfp-img-mobile',
                                            item.image = {
                                                tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
                                            }
                                    }
                                }
                            }
                        })
                    })
                })

                function providerAddress(provider_id, provider_address_id = "") {
                    var provider_address_route =
                        "{{ route('ajax-list', ['type' => 'provider_address', 'provider_id' => '']) }}" + provider_id;
                    provider_address_route = provider_address_route.replace('amp;', '');

                    $.ajax({
                        url: provider_address_route,
                        success: function(result) {
                            $('#provider_address_id').select2({
                                width: '100%',
                                placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.provider_address')]) }}",
                                data: result.results
                            });
                            if (provider_address_id != "") {
                                $('#provider_address_id').val(provider_address_id.split(',')).trigger('change');
                            }
                        }
                    });
                }

                function getSubCategory(category_id, subcategory_id = "") {
                    var get_subcategory_list =
                        "{{ route('ajax-list', ['type' => 'subcategory_list', 'category_id' => '']) }}" + category_id;
                    get_subcategory_list = get_subcategory_list.replace('amp;', '');

                    $.ajax({
                        url: get_subcategory_list,
                        success: function(result) {
                            $('#subcategory_id').select2({
                                width: '100%',
                                placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.subcategory')]) }}",
                                data: result.results
                            });
                            if (subcategory_id != "") {
                                $('#subcategory_id').val(subcategory_id).trigger('change');
                            }
                        }
                    });
                }
                var price = "{{ isset($servicedata->price) ? $servicedata->price : '' }}";
                var discount = "{{ isset($servicedata->discount) ? $servicedata->discount : '' }}";

                function priceformat(value) {
                    if (value == 'free') {
                        $('#price').val(0);
                        $('#price').attr("readonly", true)

                        $('#discount').val(0);
                        $('#discount').attr("readonly", true)

                    } else {
                        $('#price').val(price);
                        $('#price').attr("readonly", false)
                        $('#discount').val(discount);
                        $('#discount').attr("readonly", false)
                    }
                }
            })(jQuery);

            document.addEventListener('DOMContentLoaded', function() {
                checkImage();
            });

            function checkImage() {
                var id = @json($servicedata->id);
                var route = "{{ route('check-image', ':id') }}";
                route = route.replace(':id', id);
                var type = 'service';

                $.ajax({
                    url: route,
                    type: 'GET',
                    data: {
                        type: type,
                    },
                    success: function(result) {
                        var attachments = result.results;

                        if (attachments && attachments.length === 0) {
                            $('input[name="service_attachment[]"]').attr('required', 'required');
                        } else {
                            $('input[name="service_attachment[]"]').removeAttr('required');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            //     $(document).ready(function () {
            //     // Function to initialize Select2 for a given element
            //     function initializeSelect2($element) {
            //         const selectedId = $element.data('selected-id'); // Get the preselected ID
            //         const ajaxUrl = $element.data('ajax--url');
            //         const placeholder = $element.data('placeholder');

            //         $element.select2({
            //             placeholder: placeholder,
            //             ajax: {
            //                 url: ajaxUrl,
            //                 dataType: 'json',
            //                 delay: 250,
            //                 data: function (params) {
            //                     return {
            //                         q: params.term, // Search term
            //                     };
            //                 },
            //                 processResults: function (data) {
            //                     return {
            //                         results: data.map(function (item) {
            //                             return { id: item.id, text: item.text };
            //                         }),
            //                     };
            //                 },
            //                 cache: true,
            //             },
            //         });

            //         // Preselect the value during edit
            //         if (selectedId) {
            //             $.ajax({
            //                 url: ajaxUrl, // Fetch the preselected item
            //                 data: { id: selectedId },
            //                 dataType: 'json',
            //                 success: function (response) {
            //                     const selectedItem = response.find(item => item.id == selectedId);
            //                     if (selectedItem) {
            //                         // Create and append the selected option
            //                         const option = new Option(selectedItem.text, selectedItem.id, true, true);
            //                         $element.append(option).trigger('change');
            //                     }
            //                 },
            //                 error: function () {
            //                     console.error('Failed to fetch selected item for:', selectedId);
            //                 },
            //             });
            //         }
            //     }
            //     function synchronizeDropdowns(type, selectedId) {
            //         $(`.select2js-${type}`).each(function () {
            //             const $dropdown = $(this);

            //             // Fetch the translated value for the selected ID
            //             $.ajax({
            //                 url: $dropdown.data('ajax--url'),
            //                 data: { id: selectedId },
            //                 dataType: 'json',
            //                 success: function (response) {
            //                     const translatedItem = response.find(item => item.id == selectedId);
            //                     if (translatedItem) {
            //                         const option = new Option(translatedItem.text, translatedItem.id, true, true);
            //                         $dropdown.empty().append(option).trigger('change');
            //                     }
            //                 },
            //             });
            //         });
            //     }
            //     // Function to update subcategory dropdown based on category selection
            //     function updateSubcategoryDropdown($categoryDropdown, $subcategoryDropdown) {
            //     // Ensure a single change listener
            //     $categoryDropdown.off('change').on('change', function () {
            //         const categoryId = $(this).val();

            //         if (!categoryId) {
            //             $subcategoryDropdown.empty().trigger('change'); // Clear subcategory
            //             return;
            //         }

            //         const subcategoryAjaxUrl = $subcategoryDropdown
            //             .data('ajax--url')
            //             .replace(/category_id=[^&]*/, `category_id=${categoryId}`);

            //         // Safely destroy Select2 instance if initialized
            //         if ($subcategoryDropdown.hasClass('select2-hidden-accessible')) {
            //             $subcategoryDropdown.select2('destroy');
            //         }

            //         $subcategoryDropdown.empty(); // Clear current options

            //         // Update the AJAX URL dynamically
            //         $subcategoryDropdown.data('ajax--url', subcategoryAjaxUrl);

            //         // Reinitialize Select2 with the new URL
            //         initializeSelect2($subcategoryDropdown);
            //     });
            // }


            //     // Initialize Select2 for all category and subcategory dropdowns
            //     $('.select2js-category').each(function () {
            //         const $categoryDropdown = $(this);
            //         console.log("Dropdown data-selected-id:", $categoryDropdown.data('selected-id'));

            //         const languageId = $categoryDropdown.data('language-id');
            //         const $subcategoryDropdown = $(`#subcategory_id_${languageId}`);

            //         // Initialize subcategory dropdown first to avoid empty state issues
            //         updateSubcategoryDropdown($categoryDropdown, $subcategoryDropdown);

            //         // Then initialize the category dropdown
            //         initializeSelect2($categoryDropdown);
            //     });
            //     // Listen for changes and synchronize all dropdowns of the same type
            //     $('[data-select2-type]').on('select2:select', function (e) {
            //         const $dropdown = $(this);
            //         const selectedId = e.params.data.id;
            //         const type = $dropdown.data('select2-type');

            //         synchronizeDropdowns(type, selectedId);
            //     });


            //     // Handle language toggle
            //     $('.language-toggle').on('click', function () {
            //         const languageId = $(this).data('language-id');
            //         $('.language-form').hide();
            //         $(`#form-language-${languageId}`).show();
            //     });
            // });
            $(document).ready(function() {
                $('#is_enable_advance_payment').on('change', function() {
                    if ($(this).is(':checked')) {
                        $('#advance_payment_amount').prop('required', true);
                    } else {
                        $('#advance_payment_amount').prop('required', false);
                    }
                });
                if ($('#is_enable_advance_payment').is(':checked')) {
                    $('#advance_payment_amount').prop('required', true);
                }
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var input = document.querySelector('input[name=meta_keywords]');
            if (input) {
                new Tagify(input, {
                    delimiters: ",",
                    whitelist: [],
                    dropdown: { enabled: 0 },
                    originalInputValueFormat: valuesArr => JSON.stringify(valuesArr.map(item => item.value))
                });
            }

            

            // SEO Enable/Disable Switch functionality
            var seoEnabledSwitch = document.getElementById('seo_enabled');
            var seoFieldsSection = document.getElementById('seo_fields_section');
            var metaTitle = document.getElementById('meta_title');
            var metaTitleCount = document.getElementById('meta-title-count');
            var metaDesc = document.getElementById('meta_description');
            var metaDescCount = document.getElementById('meta-desc-count');
            var metaKeywords = document.getElementById('meta_keywords');
            var seoImage = document.querySelector('input[name="seo_image"]');

            function updateMetaTitleCount() {
                if (metaTitle && metaTitleCount) {
                    metaTitleCount.textContent = metaTitle.value.length;
                }
            }
            function updateMetaDescCount() {
                if (metaDesc && metaDescCount) {
                    metaDescCount.textContent = metaDesc.value.length;
                }
            }

            // Attach listeners
            if (metaTitle) {
                metaTitle.addEventListener('input', updateMetaTitleCount);
                updateMetaTitleCount();
            }
            if (metaDesc) {
                metaDesc.addEventListener('input', updateMetaDescCount);
                updateMetaDescCount();
            }

            function toggleSeoFields() {
                if (seoEnabledSwitch.checked) {
                    seoFieldsSection.style.display = 'block';
                    // Do not restore old data, keep fields as is (empty if just toggled on)
                } else {
                    seoFieldsSection.style.display = 'none';
                    // Clear SEO fields when disabling
                    if (metaTitle) {
                        metaTitle.value = '';
                        if (metaTitleCount) metaTitleCount.textContent = '0';
                    }
                    if (metaDesc) {
                        metaDesc.value = '';
                        if (metaDescCount) metaDescCount.textContent = '0';
                    }
                    if (metaKeywords) {
                        metaKeywords.value = '';
                        if (metaKeywords.tagify) metaKeywords.tagify.removeAllTags();
                    }
                    if (seoImage) {
                        seoImage.value = '';
                        var seoImagePreview = document.getElementById('seo_image_preview');
                        if (seoImagePreview) {
                            seoImagePreview.src = '';
                            seoImagePreview.style.display = 'none';
                        }
                    }
                }
                // Always update counts after toggling
                updateMetaTitleCount();
                updateMetaDescCount();
            }

            // Initial state: show/hide and populate fields based on backend data
            if (seoEnabledSwitch) {
                if (seoEnabledSwitch.checked) {
                    seoFieldsSection.style.display = 'block';
                    // The Blade template will have already populated the fields with $servicedata values
                } else {
                    seoFieldsSection.style.display = 'none';
                    // Clear fields (in case of browser autofill)
                    if (metaTitle) metaTitle.value = '';
                    if (metaDesc) metaDesc.value = '';
                    if (metaKeywords) {
                        metaKeywords.value = '';
                        if (metaKeywords.tagify) metaKeywords.tagify.removeAllTags();
                    }
                    if (seoImage) {
                        seoImage.value = '';
                        var seoImagePreview = document.getElementById('seo_image_preview');
                        if (seoImagePreview) {
                            seoImagePreview.src = '';
                            seoImagePreview.style.display = 'none';
                        }
                    }
                    if (metaTitleCount) metaTitleCount.textContent = '0';
                    if (metaDescCount) metaDescCount.textContent = '0';
                }
                // Add event listener
                seoEnabledSwitch.addEventListener('change', toggleSeoFields);
            }
        });
        </script>
        <script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // 10MB in bytes
    const MAX_SIZE = 10 * 1024 * 1024;

    // SEO Image validation (10MB limit)
    const seoImageInput = document.querySelector('input[name="seo_image"]');
    if (seoImageInput) {
        seoImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            const errorBlock = seoImageInput.parentElement.querySelector('.help-block.with-errors.text-danger');
            if (file) {
                if (file.size > MAX_SIZE) {
                    event.target.value = '';
                    if (errorBlock) {
                        errorBlock.textContent = 'Image size must be less than 10MB.';
                    } else {
                        alert('Image size must be less than 10MB.');
                    }
                    var preview = document.getElementById('seo_image_preview');
                    if (preview) preview.style.display = 'none';
                    seoImageInput.setAttribute('data-has-image', '0');
                } else {
                    if (errorBlock) errorBlock.textContent = '';
                }
            } else {
                seoImageInput.setAttribute('data-has-image', seoImageInput.value ? '1' : '0');
                if (errorBlock) errorBlock.textContent = '';
            }
        });
    }

    // Service Attachment validation (10MB limit per file)
    const serviceAttachmentInputs = document.querySelectorAll('input[name="service_attachment[]"]');
    serviceAttachmentInputs.forEach(function(input) {
        input.addEventListener('change', function(event) {
            const files = event.target.files;
            const errorBlock = input.parentElement.querySelector('.help-block.with-errors.text-danger');
            let tooLarge = false;
            for (let i = 0; i < files.length; i++) {
                if (files[i].size > MAX_SIZE) {
                    tooLarge = true;
                    break;
                }
            }
            if (tooLarge) {
                event.target.value = '';
                if (errorBlock) {
                    errorBlock.textContent = 'Each image must be less than 10MB.';
                } else {
                    alert('Each image must be less than 10MB.');
                }
                var preview = document.getElementById('service_attachment_preview');
                if (preview) preview.style.display = 'none';
            } else {
                if (errorBlock) errorBlock.textContent = '';
            }
        });
    });
});
</script>
@endsection
</x-master-layout>
