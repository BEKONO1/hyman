<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                        <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        @if($auth_user->can('servicepackage list'))
                        <a href="{{ route('servicepackage.index') }}" class=" float-end btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{ html()->form('POST', route('servicepackage.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('servicepackage')->open()}}
                    {{ html()->hidden('id',$servicepackage->id ?? null) }}    
                    
                    @include('partials._language_toggale')
@foreach($language_array as $language)
    <div id="form-language-{{ $language['id'] }}" class="language-form" style="display: {{ $language['id'] == app()->getLocale() ? 'block' : 'none' }};">
        <div class="row">
            @foreach(['name' => __('messages.name'), 'description' => __('messages.description')] as $field => $label)
                <div class="form-group col-md-{{ $field === 'name' ? '4' : '4' }}">
                    {{ html()->label($label . ($field === 'name' ? ' <span class="text-danger">*</span>' : ''), $field)->class('form-control-label language-label') }}
                    @php
                        $value = $language['id'] == 'en' 
                            ? $servicepackage ? $servicepackage->translate($field, 'en') : '' 
                            : ($servicepackage ? $servicepackage->translate($field, $language['id']) : '');
                        $name = $language['id'] == 'en' ? $field : "translations[{$language['id']}][$field]";
                    @endphp

                    @if($field === 'name')
                        {{ html()->text($name, $value)
                            ->placeholder($label)
                            ->class('form-control')
                            ->attribute('title', 'Please enter alphabetic characters and spaces only')
                            ->attribute('data-required', 'true') }}
                    @else
                        {{ html()->textarea($name, $value)
                            ->class('form-control textarea')
                            ->rows(3)
                            ->placeholder($label) }}
                    @endif

                    <small class="help-block with-errors text-danger"></small>
                </div>
            @endforeach

            @if(auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                <!-- Always Render Provider Dropdown (We'll Show/Hide with JS) -->
                <div class="form-group col-md-4" id="provider_id_wrapper_{{ $language['id'] }}">
                    {{ html()->label(__('messages.select_name', ['select' => __('messages.provider')]) . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                    <br />
                    <select name="provider_id"
                            id="provider_id_{{ $language['id'] }}"
                            class="form-select select2js-provider"
                            data-select2-type="provider"
                            data-selected-id="{{ optional($servicepackage->providers)->id ?? '' }}"
                            data-language-id="{{ $language['id'] }}"
                            data-ajax--url="{{ route('ajax-list', ['type' => 'provider', 'language_id' => $language['id']]) }}"
                            data-placeholder="{{ __('messages.select_name', ['select' => __('messages.provider')]) }}" >
                    </select>
                </div>
            @endif

            <!-- Package Type Selection -->
            <div class="form-group col-md-4">
                {{ html()->label(__('messages.package_type'), 'package_type')->class('form-control-label') }}
                {{ html()->select('package_type', ['single' => __('messages.single'), 'multiple' => __('messages.multiple')], $servicepackage->package_type)
                    ->class('form-control')
                    ->id('package_type_' . $language['id']) 
                    ->required() }}
            </div>

            <!-- Category Selection -->
            <div class="form-group col-md-4 d-none" id="select_category_{{ $language['id'] }}">
                {{ html()->label(__('messages.select_name', ['select' => __('messages.category')]) . ' <span class="text-danger">*</span>', 'category_id')->class('form-control-label') }}
                <select name="category_id"
                        id="category_id_{{ $language['id'] }}"  
                        class="form-select select2js-category"
                        data-select2-type="category"
                        data-selected-id="{{ $servicepackage->category_id ?? '' }}"
                        data-language-id="{{ $language['id'] }}"
                        data-ajax--url="{{ route('ajax-list', ['type' => 'category', 'language_id' => $language['id']]) }}"
                        data-placeholder="{{ __('messages.select_name', ['select' => __('messages.category')]) }}" >
                </select>
                <small class="help-block with-errors text-danger"></small>
            </div>

            <!-- SubCategory Selection -->
            <div class="form-group col-md-4 d-none" id="select_subcategory_{{ $language['id'] }}">
                {{ html()->label(__('messages.select_name', ['select' => __('messages.subcategory')]) , 'subcategory_id')->class('form-control-label') }}
                <select name="subcategory_id"
                        id="subcategory_id_{{ $language['id'] }}"  
                        class="form-select select2js-subcategory"
                        data-select2-type="subcategory"
                        data-selected-id="{{ $servicepackage->subcategory_id ?? '' }}"
                        data-language-id="{{ $language['id'] }}"
                        data-ajax--url="{{ route('ajax-list', ['type' => 'subcategory', 'category_id' => $servicepackage->category_id ?? '', 'language_id' => $language['id']]) }}"
                        data-placeholder="{{ __('messages.select_name', ['select' => __('messages.subcategory')]) }}" >
                </select>
                <small class="help-block with-errors text-danger"></small>
            </div>

             <!-- Service Selection -->
            <div class="form-group col-md-4">
                {{ html()->label(__('messages.select_name', ['select' => __('messages.service')]) . ' <span class="text-danger">*</span>', 'service_id')->class('form-control-label') }}
                <select name="service_id[]"
                        id="service_id_{{ $language['id'] }}"
                        class="form-select select2js-service service_id"
                        data-select2-type="service"
                        data-selected-id="{{ is_array($selectedServiceId) ? implode(',', $selectedServiceId) : $selectedServiceId }}"
                        data-language-id="{{ $language['id'] }}"
                        data-ajax--url="{{ route('ajax-list', ['type' => 'service-list','provider_id' => optional($servicepackage->providers)->id ?? '','category_id' => $servicepackage->category_id ?? '','subcategory_id' => $servicepackage->subcategory_id ?? '', 'language_id' => $language['id']]) }}"
                        data-placeholder="{{ __('messages.select_name', ['select' => __('messages.service')]) }}"
                        multiple>
                </select>
                <small class="help-block with-errors text-danger"></small>
            </div>
        </div>
    </div>
@endforeach

                    
                    
                    <div class="row">
                   
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.start_at'), 'start_at')->class('form-control-label') }}
                            {{ html()->text('start_at', $servicepackage->start_at)->placeholder(__('messages.start_at'))->class('form-control min-datepicker')}}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(__('messages.end_at'), 'end_at')->class('form-control-label') }}
                            {{ html()->text('end_at', $servicepackage->end_at)->placeholder(__('messages.end_at'))->class('form-control min-datepicker')}}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-md-4" id="price_div">
                            {{ html()->label(__('messages.price') . ' <span class="text-danger">*</span>', 'price')->class('form-control-label') }}
                            {{ html()->number('price', $servicepackage->price)->attributes(['min' => 1, 'step' => 'any'])->placeholder(__('messages.price'))->class('form-control')->required()->id('price')}}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-md-4" id="service_total_price">
                            {{ html()->label(__('messages.original_price'), 'original_price')->class('form-control-label') }}
                            {{ html()->number('original_price', null)->attributes(['min' => 1, 'step' => 'any'])->placeholder(__('messages.original_price'))->class('form-control')->id('original_price')->attribute('readonly', 'readonly')}}
                            <small class="help-block with-errors text-danger"></small>
                        </div>
                        <div class="form-group col-md-4">
                            {{ html()->label(trans('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                            {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')],  $servicepackage->status)->id('role')->class('form-select select2js')->required()}}
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label" for="package_attachment">{{ __('messages.image') }} <span class="text-danger">*</span> </label>
                            <div class="custom-file">
                            <input type="file" name="package_attachment[]" class="custom-file-input"  data-file-error="{{ __('messages.files_not_allowed') }}" multiple >
                                    <label class="custom-file-label upload-label">{{ __('messages.choose_file',['file' =>  __('messages.attachments') ]) }}</label>
                            </div>
                        </div>
                        <!-- <div class="form-group col-md-12">
                            {{ html()->label(trans('messages.description'), 'description')->class('form-control-label') }}
                            {{ html()->textarea('description', $servicepackage->description)->class('form-control textarea')->rows(3)->placeholder(__('messages.description'))}}
                        </div> -->
                    </div>
                    <div class="row package_attachment_div">
                            <div class="col-md-12">
                                @if(getMediaFileExit($servicepackage, 'package_attachment'))
                                @php
                                $attchments = $servicepackage->getMedia('package_attachment');
                                $file_extention = config('constant.IMAGE_EXTENTIONS');
                                @endphp
                                <div class="border-start">
                                    <p class="ms-2"><b>{{ __('messages.attached_files') }}</b></p>
                                    <div class="ms-2 my-3">
                                        <div class="row service_attachment_div">
                                            @foreach($attchments as $attchment )
                                            <?php
                                            $extention = in_array(strtolower(imageExtention($attchment->getFullUrl())), $file_extention);
                                            ?>

                                            <div class="col-md-2 pe-10 text-center galary file-gallary-{{$servicepackage->id}} position-relative" data-gallery=".file-gallary-{{$servicepackage->id}}" id="package_attachment_preview_{{$attchment->id}}">
                                                @if($extention)
                                                <a id="attachment_files" href="{{ $attchment->getFullUrl() }}" class="list-group-item-action attachment-list" target="_blank">
                                                    <img src="{{ $attchment->getFullUrl() }}" class="attachment-image" alt="">
                                                </a>
                                                @else
                                                <a id="attachment_files" class="video list-group-item-action attachment-list" href="{{ $attchment->getFullUrl() }}">
                                                    <img src="{{ asset('images/file.png') }}" class="attachment-file">
                                                </a>
                                                @endif
                                                <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $attchment->id, 'type' => 'package_attachment']) }}" data--submit="confirm_form" data--confirmation='true' data--ajax="true" data-toggle="tooltip" title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.attachments") ] ) }}' data-title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.attachments") ] ) }}' data-message='{{ __("messages.remove_file_msg") }}'>
                                                    <i class="ri-close-circle-line"></i>
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-switch custom-control-inline">
                                {{ html()->checkbox('is_featured', $servicepackage->is_featured)->class('custom-control-input')->id('is_featured')}}
                                <label class="custom-control-label" for="is_featured">{{ __('messages.set_as_featured')  }}
                                </label>
                            </div>
                        </div>
                    </div>
                    {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end') }}
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>
</div>

@section('bottom_script')
    <script type="text/javascript">
        // (function($) {
        //     "use strict";
        //     $(document).ready(function(){
        //         var package_type = $("#package_type").val();
        //         hideShow(package_type);

        //         $(document).on('change', '#package_type', function() {
        //             var package_type = $(this).val();
        //             hideShow(package_type);
        //         })

        //         var category_id = "{{ isset($servicepackage->category_id) ? $servicepackage->category_id : '' }}";
        //         var subcategory_id = "{{ isset($servicepackage->subcategory_id) ? $servicepackage->subcategory_id : '' }}";
        //         var provider_id = "{{ isset($servicepackage->provider_id) ? $servicepackage->provider_id : '' }}";
        //         var service_id = "{{$servicepackage->packageServices->pluck('service_id')->implode(',')}}"
        //         if(service_id !== ''){
        //             getService(provider_id, category_id, subcategory_id, service_id, true);  // Pass 'true' to indicate it's in edit mode
        //         }
        //         getSubCategory(category_id, subcategory_id)
        //         getService(provider_id)
        //         $(document).on('change', '#provider_id', function() {
        //             var provider_id = $(this).val();
        //             $('#custom_service_id').empty();
        //             $('#original_price').val(0);
        //             $('#category_id').empty();
        //             $('#subcategory_id').empty();
        //             getService(provider_id,category_id)
        //         })

        //            $(document).on('change', '#package_type', function() {

        //             var provider_id=$('#provider_id').val();

        //             $('#custom_service_id').empty();
        //             getService(provider_id)
        //         })



        //         $(document).on('change', '#category_id', function() {
        //             var category_id = $(this).val();
        //             var provider_id = $('#provider_id').val();
        //             var subcategory_id = $('#subcategory_id').val();


        //             $('#subcategory_id').empty();
        //             getSubCategory(category_id, subcategory_id);

        //             $('#custom_service_id').empty();
        //             getService(provider_id,category_id,subcategory_id)
        //         })

        //         $(document).on('change', '#subcategory_id', function() {
        //             var subcategory_id = $(this).val();
        //             var category_id = $('#category_id').val();
        //             var provider_id = $('#provider_id').val();
        //             var selectedServiceIds = $('#custom_service_id').val();

        //             $('#custom_service_id').empty();
        //             getService(provider_id,category_id,subcategory_id,selectedServiceIds)
        //         })
        //     })
            
        //     function hideShow(package_type){
        //         if(package_type == 'single'){
        //             $('.select2js-subcategory').removeClass('d-none');
        //             $('.select2js-category').removeClass('d-none');
        //             $('#category_id').prop('required', true);
        //             $('#subcategory_id').prop('required', true);
        //         } 
        //         else{
        //             $('.select2js-subcategory').addClass('d-none');
        //             $('.select2js-category').addClass('d-none');
        //             $('#category_id').prop('required', false);
        //             $('#subcategory_id').prop('required', false);
        //         }
        //     }
        //     function getSubCategory(category_id, subcategory_id = "") {
        //         var get_subcategory_list = "{{ route('ajax-list', [ 'type' => 'subcategory_list','category_id' =>'']) }}" + category_id;
        //         get_subcategory_list = get_subcategory_list.replace('amp;', '');

        //         $.ajax({
        //             url: get_subcategory_list,
        //             success: function(result) {
        //                 $('#subcategory_id').select2({
        //                     width: '100%',
        //                     placeholder: "{{ trans('messages.select_name',['select' => trans('messages.subcategory')]) }}",
        //                     data: result.results
        //                 });
        //                 if (subcategory_id != "") {
        //                     $('#subcategory_id').val(subcategory_id).trigger('change');
        //                 }
        //             }
        //         });
        //     }
        //     function getService(provider_id,category_id,subcategory_id,service_id='',isEdit = false){
        //         var selectedServiceId = {!! json_encode($selectedServiceId) !!};
        //         $.ajax({
        //             url: "{{ route('service-list') }}",
        //             method:"POST",
        //             data : { '_token': $('meta[name=csrf-token]').attr('content'),provider_id : provider_id,category_id:category_id,subcategory_id:subcategory_id },
                   
        //             success: function(result) {
        //                 console.log(result)
        //                 $('#custom_service_id').select2({
        //                     width: '100%',
        //                     placeholder: "{{ trans('messages.select_name',['select' => trans('messages.subcategory')]) }}",
        //                     data: result.results
        //                 });
        //                 // Preselect services if in edit mode
        //                 if (selectedServiceId && selectedServiceId.length) {
        //                     selectedServiceId.forEach(function(id) {
        //                         $('#custom_service_id option[value="' + id + '"]').prop('selected', true);
        //                     });
        //                     $('#custom_service_id').trigger('change');  // Trigger change to calculate price
        //                     calculateTotalPrice(result.results, selectedServiceId);  // Manually calculate the total price
        //                 }

        //                 // Handle service selection and total price calculation
        //                 $('#custom_service_id').on('change', function() {
        //                     var selectedServices = $(this).val();
        //                     calculateTotalPrice(result.results, selectedServices);
        //                 });
                        
        //             }
        //         });
        //     }

        //     function calculateTotalPrice(serviceList, selectedServices) {
        //         var totalServicePrice = 0;

        //         selectedServices.forEach(function(serviceId) {
        //             var selectedService = serviceList.find(service => service.id == serviceId);
        //             if (selectedService && selectedService.price) {
        //                 totalServicePrice += parseFloat(selectedService.price);
        //             }
        //         });

        //         // Set the total price
        //         $('#original_price').val(totalServicePrice);
        //     }
        // })(jQuery);
        document.addEventListener('DOMContentLoaded', function() { 
    checkImage();
});
function checkImage() { 
    var id = @json($servicepackage->id); 
    var route = "{{ route('check-image', ':id') }}";
    route = route.replace(':id', id);  
    var type = 'package_attachment';

    $.ajax({
        url: route,
        type: 'GET',   
        data: {
            type: type,   
        }, 
        success: function(result) {  
           
            if (attachments.length === 0) { 
                $('input[name="package_attachment[]"]').attr('required', 'required');
            } else { 
                $('input[name="package_attachment[]"]').removeAttr('required');
            }         
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);  
        }
    });
}

function hideShow(package_type) {
    // Loop through all languages and show/hide category and subcategory fields
    @foreach($language_array as $language)
        var languageId = '{{ $language['id'] }}';
        var categorySelector = '#category_id_' + languageId;
        var subcategorySelector = '#subcategory_id_' + languageId;
        var selectCategoryWrapper = '#select_category_' + languageId;
        var selectSubcategoryWrapper = '#select_subcategory_' + languageId;

        if (package_type === 'single') {
            // Show category and subcategory for all languages
            $(selectCategoryWrapper).removeClass('d-none');
            $(selectSubcategoryWrapper).removeClass('d-none');
            $(categorySelector).prop('required', true); // Make category required
            $(subcategorySelector).prop('required', true); // Make subcategory required
        } else {
            // Hide category and subcategory for all languages
            $(selectCategoryWrapper).addClass('d-none');
            $(selectSubcategoryWrapper).addClass('d-none');
            $(categorySelector).prop('required', false); // Remove category requirement
            $(subcategorySelector).prop('required', false); // Remove subcategory requirement
        }
    @endforeach
}

$(document).ready(function() {
    // Trigger the hideShow function when any package_type dropdown is changed
    @foreach($language_array as $language)
        $('#package_type_{{ $language['id'] }}').on('change', function() {
            var package_type = $(this).val(); // Get the selected package type
            // Update all package_type dropdowns to match the selected value
            @foreach($language_array as $innerLanguage)
                $('#package_type_{{ $innerLanguage['id'] }}').val(package_type);
            @endforeach
            hideShow(package_type); // Call the function to show/hide fields
        });

        // Trigger the hideShow function initially based on the current value of package_type
        var initialPackageType = $('#package_type_{{ $language['id'] }}').val();
        hideShow(initialPackageType);
    @endforeach
});

    </script>
@endsection
</x-master-layout>