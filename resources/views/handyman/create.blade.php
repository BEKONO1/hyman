<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            <a href="{{ route('handyman.index') }}" class=" float-end btn btn-sm btn-primary"><i
                                    class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @if($auth_user->can('handyman list'))
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('handyman.store'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->id('user-form')->open() }}
                        {{ html()->hidden('id',$handymandata->id ?? null) }}
                        {{ html()->hidden('user_type', 'handyman') }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.first_name').' <span class="text-danger">*</span>', 'first_name')->class('form-control-label') }}
                                {{ html()->text('first_name', $handymandata->first_name)->placeholder(__('messages.first_name'))->class('form-control')->required() }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.last_name').' <span class="text-danger">*</span>', 'last_name')->class('form-control-label') }}
                                {{ html()->text('last_name', $handymandata->last_name)->placeholder(__('messages.last_name'))->class('form-control')->required() }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.username').' <span class="text-danger">*</span>', 'username')->class('form-control-label') }}
                                {{ html()->text('username', $handymandata->username)->placeholder(__('messages.username'))->class('form-control')->required() }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.email').' <span class="text-danger">*</span>', 'email')->class('form-control-label') }}
                                {{ html()->email('email', $handymandata->email)->placeholder(__('messages.email'))->class('form-control')->required()->attribute('pattern', '[^@]+@[^@]+\.[a-zA-Z]{2,}')->attribute('title', 'Please enter a valid email address') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                    
                            @if (!isset($handymandata->id) || $handymandata->id == null)
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.password').' <span class="text-danger">*</span>', 'password')->class('form-control-label') }}
                                {{ html()->password('password')->class('form-control')->placeholder(__('messages.password'))->required()->attribute('autocomplete', 'new-password') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            @endif
                            @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.providers')]).' <span class="text-danger">*</span>', 'provider_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('provider_id', [optional($handymandata->providers)->id => optional($handymandata->providers)->display_name], optional($handymandata->providers)->id)
                                    ->class('select2js form-group providers')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.providers')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'provider'])) 
                                    }}
                            </div>
                            @endif
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.handymantype')]).' <span class="text-danger">*</span>', 'handymantype_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('handymantype_id', [], old('handymantype_id'))
                                    ->class('select2js form-group handymantype_id')
                                    ->id('handymantype_id')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.handymantype')])) 
                                    
                                    }}
                            </div>
<!--                     
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.provider_address')]).' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                <br />
                                {{ html()->select('service_address_id', [], old('service_address_id'))
                                    ->class('select2js form-group service_address_id')
                                    ->id('service_address_id')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.provider_address')])) 
                                    }}
                            </div> -->
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.country')]).' <span class="text-danger">*</span>', 'country_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('country_id', [optional($handymandata->country)->id => optional($handymandata->country)->name], optional($handymandata->country)->id)
                                    ->class('select2js form-group country')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.country')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country'])) 
                                    }}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.state')]).' <span class="text-danger">*</span>', 'state_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('state_id', [], [])
                                    ->class('select2js form-group state_id')
                                    ->required()
                                    ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.state')]))       
                                    }}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.select_name', ['select' => __('messages.city')]).' <span class="text-danger">*</span>', 'city_id')->class('form-control-label') }}
                                <br />
                                {{ html()->select('city_id', [], old('city_id'))
                                    ->class('select2js form-group city_id')->required()->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.city')])) }}
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.contact_number').' <span class="text-danger">*</span>', 'contact_number')->class('form-control-label') }}
                                {{ html()->text('contact_number', $handymandata->contact_number)->placeholder(__('messages.contact_number'))->class('form-control contact_number')->required() }}
                                      {{-- //'maxlength' => 20, // Maximum 20 characters allowed
                                      //'pattern' => '^(\+|-)?\d+$', // Accepts '+' and numeric characters only --}}
                                <small class="help-block with-errors text-danger" id="contact_number_err"></small>
                            </div>
                    
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.status').' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], old('status'))->class('form-select select2js')->required() }}
                            </div>
                    
                            <div class="form-group col-md-4">
                                <label class="form-control-label" for="profile_image">{{ __('messages.profile_image') }}
                                </label>
                                <div class="custom-file">
                                    <input type="file" name="profile_image" class="custom-file-input" accept="image/*">
                                    <label
                                        class="custom-file-label upload-label">{{  __('messages.choose_file',['file' =>  __('messages.profile_image') ]) }}</label>
                                </div>
                                <!-- <span class="selected_file"></span> -->
                            </div>
                    
                            @if(getMediaFileExit($handymandata, 'profile_image'))
                            <div class="col-md-2 mb-2 position-relative">
                                <img id="profile_image_preview" src="{{getSingleMedia($handymandata,'profile_image')}}"
                                    alt="#" class="attachment-image mt-1">
                                <a class="text-danger remove-file"
                                    href="{{ route('remove.file', ['id' => $handymandata->id, 'type' => 'profile_image']) }}"
                                    data--submit="confirm_form" data--confirmation='true' data--ajax="true"
                                    data-toggle="tooltip"
                                    title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.image") ]) }}'
                                    data-title='{{ __("messages.remove_file_title" , ["name" =>  __("messages.image") ]) }}'
                                    data-message='{{ __("messages.remove_file_msg") }}'>
                                    <i class="ri-close-circle-line"></i>
                                </a>
                            </div>
                            @endif
                    
                            <div class="form-group col-md-12">
                                {{ html()->label(__('messages.address'), 'address')->class('form-control-label') }}
                                {{ html()->textarea('address')->class('form-control textarea')->rows(3)->placeholder(__('messages.address')) }}
                            </div>
                        </div>
                        {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    @section('bottom_script')
    <script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function() {
            var country_id = "{{ isset($handymandata->country_id) ? $handymandata->country_id : 0 }}";
            var state_id = "{{ isset($handymandata->state_id) ? $handymandata->state_id : 0 }}";
            var city_id = "{{ isset($handymandata->city_id) ? $handymandata->city_id : 0 }}";

            var provider_id = "{{ isset($handymandata->provider_id) ? $handymandata->provider_id : '' }}";
            var service_address_id =
                "{{ isset($handymandata->service_address_id) ? $handymandata->service_address_id : 0 }}";
            var handymantype_id =
                "{{ isset($handymandata->handymantype_id) ? $handymandata->handymantype_id : 0 }}";

            stateName(country_id, state_id);
            providerAddress(provider_id, service_address_id)
            handymanType(provider_id, handymantype_id)
            $(document).on('change', '#country_id', function() {
                var country = $(this).val();
                $('#state_id').empty();
                $('#city_id').empty();
                stateName(country);
            })
            $(document).on('change', '#state_id', function() {
                var state = $(this).val();
                $('#city_id').empty();
                cityName(state, city_id);
            })
            $(document).on('change', '#provider_id', function() {
                var provider_id = $(this).val();
                $('#service_address_id').empty();
                $('#handymantype_id').empty();
                providerAddress(provider_id, service_address_id);
                handymanType(provider_id, handymantype_id)
            })

                // Initialize intl-tel-input for the contact number
    var input = document.querySelector("#contact_number");
    var iti = window.intlTelInput(input, {
        initialCountry: "in", // Set the default country code
        separateDialCode: true, // Show the dial code separately
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js" // To handle number formatting
    });

    $(input).on('change', function() {
    var full = iti.getNumber(); 
    var code = '+' + iti.getSelectedCountryData().dialCode; 
    var number = full.replace(code, ''); 
    number = number.replace(/^0+/, ''); 
    $('#contact_number').val(code + ' ' + number);
});

    // Handle form submission
    $('#user-form').on('submit', function(e) {
        if (!iti.isValidNumber()) {
            e.preventDefault(); // Prevent form submission if the number is invalid
            $('#contact_number-error').text('Please enter a valid mobile number.').show(); // Show error message
        } else {
            $('#contact_number-error').hide();
        var full = iti.getNumber();
        var code = '+' + iti.getSelectedCountryData().dialCode;
        var number = full.replace(code, '');
        $('#contact_number').val(code + ' ' + number);
    }
});

        })
        $(document).on('keyup', '.contact_number', function() {
            var contactNumberInput = document.getElementById('contact_number');
            var inputValue = contactNumberInput.value;
            inputValue = inputValue.replace(/[^0-9+\- ]/g, '');
            if (inputValue.length > 15) {
                inputValue = inputValue.substring(0, 15);
                $('#contact_number_err').text('Contact number should not exceed 15 characters');
            } else {
                $('#contact_number_err').text('');
            }
            contactNumberInput.value = inputValue;
            if (inputValue.match(/^[0-9+\- ]+$/)) {
                $('#contact_number_err').text('');
            } else {
                $('#contact_number_err').text('Please enter a valid mobile number');
            }
        });


        function stateName(country, state = "") {
            var state_route = "{{ route('ajax-list', [ 'type' => 'state','country_id' =>'']) }}" + country;
            state_route = state_route.replace('amp;', '');

            $.ajax({
                url: state_route,
                success: function(result) {
                    $('#state_id').select2({
                        width: '100%',
                        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.state')]) }}",
                        data: result.results
                    });
                    if (state != null) {
                        $("#state_id").val(state).trigger('change');
                    }
                }
            });
        }

        function cityName(state, city = "") {
            var city_route = "{{ route('ajax-list', [ 'type' => 'city' ,'state_id' =>'']) }}" + state;
            city_route = city_route.replace('amp;', '');

            $.ajax({
                url: city_route,
                success: function(result) {
                    $('#city_id').select2({
                        width: '100%',
                        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.city')]) }}",
                        data: result.results
                    });
                    if (city != null || city != 0) {
                        $("#city_id").val(city).trigger('change');
                    }
                }
            });
        }

        function providerAddress(provider_id, service_address_id = "") {
            var provider_address_route =
                "{{ route('ajax-list', [ 'type' => 'provider_address','provider_id' =>'']) }}" + provider_id;
            provider_address_route = provider_address_route.replace('amp;', '');

            $.ajax({
                url: provider_address_route,
                success: function(result) {
                    $('#service_address_id').select2({
                        width: '100%',
                        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.provider_address')]) }}",
                        data: result.results
                    });
                    if (service_address_id != "") {
                        $('#service_address_id').val(service_address_id).trigger('change');
                    }
                }
            });
        }

        function handymanType(provider_id, handymantype_id = "") {
            var handymantype_route =
                "{{ route('ajax-list', [ 'type' => 'handymantype','provider_id' =>'']) }}" + provider_id;
            handymantype_route = handymantype_route.replace('amp;', '');

            $.ajax({
                url: handymantype_route,
                success: function(result) {
                    $('#handymantype_id').select2({
                        width: '100%',
                        placeholder: "{{ trans('messages.select_name',['select' => trans('messages.handymantype')]) }}",
                        data: result.results
                    });
                    if (handymantype_id != "") {
                        $('#handymantype_id').val(handymantype_id).trigger('change');
                    }
                }
            });
        }
    })(jQuery);
    </script>
    @endsection
</x-master-layout>