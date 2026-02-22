<x-guest-layout>
   <section class="login-content">
      <div class="container h-100">
         <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-5">
               <div class="card p-3">
                  <div class="card-body">
                     <div class="auth-logo">
                        <a href="{{route('frontend.index')}}">
                           <img src="{{ getSingleMedia(imageSession('get'),'logo',null) }}" class="img-fluid rounded-normal" alt="logo">
                        </a>
                     </div>
                     <h3 class="mb-3 fw-bold text-center">{{__('auth.get_start')}}</h3>
                     <!-- Session Status -->
                     <x-auth-session-status class="mb-4" :status="session('status')" />

                     <!-- Validation Errors -->
                     <x-auth-validation-errors class="mb-4" :errors="$errors" />
                     <form method="POST" action="{{ route('register') }}" data-bs-toggle="validator" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="row">
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="username" class="text-secondary">{{__('auth.username')}} <span class="text-danger">*</span></label>
                                 <input class="form-control" id="username" name="username" value="{{old('username')}}" required placeholder="{{ __('auth.enter_name',[ 'name' => __('auth.username') ]) }}">
                                 <small id="username-error" class="help-block with-errors text-danger"></small>
                              </div>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="first_name" class="text-secondary">{{__('auth.first_name')}} <span class="text-danger">*</span></label>
                                 <input class="form-control" id="first_name" name="first_name" value="{{old('first_name')}}" required placeholder="{{ __('auth.enter_name',[ 'name' => __('auth.first_name') ]) }}">
                                 <small class="help-block with-errors text-danger"></small>
                              </div>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="last_name" class="text-secondary">{{__('auth.last_name')}} <span class="text-danger">*</span></label>
                                 <input class="form-control" id="last_name" name="last_name" value="{{old('last_name')}}" required placeholder="{{ __('auth.enter_name',[ 'name' => __('auth.last_name') ]) }}">
                                 <small class="help-block with-errors text-danger"></small>
                              </div>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="email" class="text-secondary">{{__('auth.email')}} <span class="text-danger">*</span></label>
                                 <input class="form-control" type="email" id="email" name="email" value="{{old('email')}}" required placeholder="{{ __('auth.enter_name',[ 'name' => __('auth.email') ]) }}" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,}">
                                 <small id="email-error" class="help-block with-errors text-danger"></small>
                              </div>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="phone_number" class="text-secondary d-block">{{__('messages.contact_number')}} <span class="text-danger">*</span></label>
                                 {{ html()->text('phone_number')->placeholder(__('messages.contact_number'))->class('form-control')->id('phone_number')->required() }}
                                 <br>
                                 <small class="help-block with-errors text-danger" id="phone_number_err"></small>
                              </div>
                                 <small id="phone_number-error" class="help-block with-errors text-danger" id="phone_number_err"></small>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="password" class="text-secondary">{{__('auth.login_password')}} <span class="text-danger">*</span></label>
                                 <input class="form-control" type="password" id="password" name="password" required autocomplete="new-password" placeholder="{{ __('auth.enter_name',[ 'name' => __('auth.login_password') ]) }}">
                                 <small class="help-block with-errors text-danger"></small>
                              </div>
                           </div>
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="password_confirmation" class="text-secondary">{{__('auth.confirm_password')}} <span class="text-danger">*</span></label>
                                 <input class="form-control" onkeyup="checkPasswordMatch()" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('auth.enter_name',[ 'name' => __('auth.confirm_password') ]) }}">
                                 <small class="help-block with-errors text-danger" id="confirm_passsword"></small>

                              </div>
                           </div>
                           <!-- Success Message -->
@if (session('success'))
   <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
   </div>
@endif
                           <!-- User Type Selection -->
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="user_type" class="text-secondary">{{ __('messages.user_type') }} <span class="text-danger">*</span></label>
                                 <select name="usertype" class="form-select select2 mb-5" id="user_type" style="width:100%">
                                    <option value="provider">{{ __('messages.provider') }}</option>
                                    <option value="handyman">{{ __('messages.handyman') }}</option>
                                 </select>
                              </div>
                           </div>

                           <!-- Zone Selection for Provider -->
                              <div class="col-lg-12 select2" id="zone_section" style="display: none;">
                                 <div class="form-group">
                                    <label for="zone_id" class="text-secondary">{{ __('messages.select_zone') }} <span class="text-danger">*</span></label>
                                    <select name="zone_id[]" class="form-select select2 mb-5" id="zone_id" style="width:100%" multiple="multiple">
                                       
                                    </select>
                                 </div>
                              </div>

                           <!-- Provider Section -->
                           <div class="col-lg-12" id="provider_section" style="display: none;">
                              <div class="form-group">
                                 <label for="providerdata" class="text-secondary">{{ __('messages.provider') }}</label>
                                 <select name="provider_id" class="form-select select2 mb-5" id="providerdata" style="width:100%">
                                    <option value="">{{ __('messages.select_provider') }}</option>
                                 </select>
                              </div>
                           </div>

                           @if(default_earning_type() !== 'subscription')
                           <!-- Commission Section -->
                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="user_commission" class="text-secondary">{{ __('messages.user_commission') }} <span class="text-danger">*</span></label>
                                 <select name="providertype_id" class="form-select select2 mb-5" id="providertype" style="width:100%">
                                    <option value="">{{ __('messages.select_provider_type') }}</option>
                                 </select>
                                 <select name="handymantype_id" class="form-select select2 mb-5 d-none" id="handymantype" style="width:100%">
                                    <option value="">{{ __('messages.select_handyman_type') }}</option>
                                 </select>
                              </div>
                           </div>
                           @endif

                           <div class="col-lg-12">
                              <div class="form-group">
                                 <label for="designation" class="text-secondary">{{__('messages.designation')}}</label>
                                 <input type="text" id="designation" name="designation" class="form-control" placeholder="{{__('placeholder.designation')}}" aria-label="designation"
                                    aria-describedby="basic-addon6">
                              </div>
                           </div>

                           <div class="col-lg-12 d-none" id="provider_document_section">
                               @foreach ($requiredDocuments as $index => $document)
                                 <div class="document-item mb-3">
                                    <label for="provider_document_{{ $index }}" class="text-secondary">
                                          {{ $document->name }}
                                          @if ($document->is_required)
                                             <span class="text-danger">*</span>
                                          @endif
                                    </label>

                                   <input type="hidden" name="document_id[]" value="{{ $document->id }}">
                                   <input type="file"
                                         name="provider_document_{{ $index }}"
                                         id="provider_document_{{ $index }}"
                                         class="form-control"
                                          data-is-required="{{ $document->is_required ? 1 : 0 }}"
                                         {{ $document->is_required ? 'required' : '' }}>
                                   
                                    <small class="help-block with-errors text-danger"></small>
                                 </div>
                              @endforeach
                           </div>
                           <div class="col-lg-12 mt-2">
                              <div class="form-check mb-3 d-flex align-items-center">
                                 <input type="checkbox" class="form-check-input mt-0" id="customCheck1" required>
                                 <label class="form-check-label ps-2" for="customCheck1">
                                    {{-- {{__('auth.agree')}} <a class="btn-link p-0 text-capitalize" href="{{ url('/') }}/term-conditions">{{__('auth.term_service')}}</a> &amp; <a class="btn-link p-0 text-capitalize" href="{{ url('/') }}/privacy-policy">{{__('auth.privacy_policy')}}</a> --}}
                                    {{ __('auth.agree') }}
                                       <a class="btn-link p-0 text-capitalize" href="{{ url('term-conditions') }}">
                                          {{ __('auth.term_service') }}
                                       </a> &amp;
                                       <a class="btn-link p-0 text-capitalize" href="{{ url('privacy-policy') }}">
                                          {{ __('auth.privacy_policy') }}
                                       </a>

                                    <small class="help-block with-errors text-danger"></small>
                                 </label>
                              </div>
                           </div>

                        </div>
                        <button type="submit" class="btn btn-primary btn-block mt-2 w-100" id="submit-btn">{{ __('auth.create_account') }}</button>
                        <div class="col-lg-12 mt-3">
                           <p class="mb-0 text-center">{{__('auth.already_have_account')}} <a class="btn-link p-0 text-capitalize" href="{{route('auth.login')}}">{{__('auth.sign_in')}}</a></p>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
       <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
       <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
       <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
       <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
       <style>
         .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
       
         border: none;
         position: relative;
      
         }
       </style>
   <script>


   $(document).ready(function() {
        const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        let isUsernameValid = true;
        let isEmailValid = true;
        let isContactNumberValid = true;
      
                function validateInput(inputSelector, fieldName, errorSelector) {
                    let debounceTimer = null;
                
                    $(inputSelector).on('input', function () {
                        let value = $(this).val().trim();
                        clearTimeout(debounceTimer);
                
                        if (fieldName === 'phone_number' && value !== '') {
                            const selectedCountry = iti.getSelectedCountryData();
                            const dialCode = selectedCountry?.dialCode || '';
                            value = `+${dialCode}${value}`;
                        }
                
                        debounceTimer = setTimeout(function () {
                            if (value !== '') {
                                $.ajax({
                                    method: 'POST',
                                    url: baseUrl + '/api/check-field', // ✅ use general endpoint
                                    data: {
                                        _token: csrfToken,
                                        field: fieldName,
                                        value: value
                                    },
                                    success: function (response) {
                                        const hasError = response.status === 'error';
                                        if (hasError) {
                                            $(errorSelector).text(`${fieldName.replace('_', ' ')} already exists.`).show();
                                        } else {
                                            $(errorSelector).text('').hide();
                                        }
                
                                        // Update validation state
                                        if (fieldName === 'username') isUsernameValid = !hasError;
                                        if (fieldName === 'email') isEmailValid = !hasError;
                                        if (fieldName === 'phone_number') isContactNumberValid = !hasError;
                                    },
                                    error: function () {
                                        $(errorSelector).text(`Error checking ${fieldName.replace('_', ' ')}.`).show();
                
                                        if (fieldName === 'username') isUsernameValid = false;
                                        if (fieldName === 'email') isEmailValid = false;
                                        if (fieldName === 'phone_number') isContactNumberValid = false;
                                    }
                                });
                            } else {
                                $(errorSelector).text('').hide();
                
                                // Reset to true if empty
                                if (fieldName === 'username') isUsernameValid = true;
                                if (fieldName === 'email') isEmailValid = true;
                                if (fieldName === 'phone_number') isContactNumberValid = true;
                            }
                        }, 300);
                    });
                }

                validateInput('#username', 'username', '#username-error');
                validateInput('#email', 'email', '#email-error');
                validateInput('#phone_number', 'phone_number', '#phone_number-error');

               });






         function checkPasswordMatch() {
            const password = $("#password").val();
            const confirmPassword = $("#password_confirmation").val();
            const errorElement = $("#confirm_passsword");
            const submitBtn = $("#submit-btn");

            if (password !== confirmPassword) {
               errorElement.text("{{ __('auth.password_mismatch_error') }}");
               submitBtn.prop("disabled", true);
            } else {
               errorElement.text("");
               submitBtn.prop("disabled", false);
            }
         }
          // Initialize select2 for zone selection
          $(document).ready(function() {
            $('#zone_id').select2({
               placeholder: "{{ __('messages.select_zone') }}",
               width: '100%'
            });
          });
         $(document).ready(function() {
            // Add function to fetch zones
            function fetchZones() {
                $.ajax({
                    url: '{{ route("ajax-list") }}',
                    type: 'GET',
                    data: {
                        type: 'zone'
                    },
                    success: function(response) {
                        const zoneDropdown = $('#zone_id');
                        zoneDropdown.empty().append($('<option>', { value: '', text: '{{ __('messages.select_zone') }}' }));

                        if (response.status === 'true') {
                            $.each(response.results, function(index, item) {
                                zoneDropdown.append($('<option>', { value: item.id, text: item.text }));
                            });
                        }
                    },
                    error: function() {
                        console.error('Error fetching zones');
                    }
                });
            }

            function fetchTypes(userType, providerId = null) {
                $.ajax({
                    url: '{{ route("ajax-list") }}',
                    type: 'GET',
                    data: {
                        type: userType === 'provider' ? 'providertype' : 'handymantype',
                        provider_id: providerId // Include provider_id if available
                    },
                    success: function(response) {
                        const providerDropdown = $('#providertype').toggleClass('d-none', userType !== 'provider');
                        const handymanDropdown = $('#handymantype').toggleClass('d-none', userType !== 'handyman');

                        if (response.status === 'true') {
                            const targetDropdown = userType === 'provider' ? providerDropdown : handymanDropdown;
                            targetDropdown.empty().append($('<option>', { value: '', text: userType === 'provider' ? '{{ __('messages.select_provider_type') }}' : '{{ __('messages.select_handyman_type') }}' }));

                            $.each(response.results, function(index, item) {
                                targetDropdown.append($('<option>', { value: item.id, text: item.text }));
                            });
                        }
                    },
                    error: function() {
                        console.error('Error fetching types');
                    }
                });
            }

            function fetchProviders() {
                var baseURL = "{{ url('/') }}";
                $.ajax({
                    url:baseURL + '/api/user-list',
                    type: 'GET',
                    data: { user_type: 'provider', status: 1, per_page: 25, page: 1 },
                    success: function(response) {
                        const providerData = $('#providerdata').empty().append($('<option>', { value: '', text: '{{ __('messages.select_provider') }}' }));

                        if (response?.data?.length) {
                            $.each(response.data, function(index, item) {
                                providerData.append($('<option>', { value: item.id, text: item.first_name + ' ' + item.last_name }));
                            });
                        } else {
                            providerData.append($('<option>', { value: '', text: '{{ __('messages.no_providers_found') }}' }));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching providers:', error);
                    }
                });
            }

            $('#user_type').change(function() {
                const selectedUserType = $(this).val();
                fetchTypes(selectedUserType);
                $('#provider_section').toggle(selectedUserType === 'handyman');
                $('#zone_section').toggle(selectedUserType === 'provider'); // Show zone section for providers
                $('#provider_document_section').toggle(selectedUserType === 'provider');
                $('#providertype').val('');
                $('#handymantype').val('');

                if (selectedUserType === 'handyman') {
                    fetchProviders();
                      $('#provider_document_section').addClass('d-none');

                     // Remove required from all document inputs
                     $('[id^="provider_document_"]').prop('required', false);
                } else if (selectedUserType === 'provider') {
                    fetchZones(); // Fetch zones when provider is selected
                     $('#provider_document_section').removeClass('d-none');
                     // Make all provider_document_* inputs required based on server-side rules
                     $('[id^="provider_document_"]').each(function () {
                           if ($(this).data('is-required') === 1) {
                              $(this).prop('required', true);
                           }
                     });

                }
                   
                  
            }).trigger('change');

            $('#providerdata').change(function() {
                if ($('#user_type').val() === 'handyman') {
                    const providerId = $(this).val();

                    fetchTypes('handyman', providerId); // Fetch handyman types based on selected provider
                }
            });
        });


var input = document.querySelector("#phone_number");
var iti = window.intlTelInput(input, {
    initialCountry: "in",
    separateDialCode: true,
    formatOnDisplay: false,
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
});

// Prevent spaces from being typed
input.addEventListener('keypress', function(e) {
    if (e.key === ' ') {
        e.preventDefault();
    }
});

// Clean number on input
input.addEventListener('input', function(e) {
    let value = e.target.value;
    value = value.replace(/\s+/g, '').replace(/[^0-9+]/g, '');
    e.target.value = value;
});

// Handle form submission
$('form').on('submit', function(e) {
    if (!iti.isValidNumber()) {
        e.preventDefault();
        $('#phone_number_err').text('Please enter a valid mobile number.');
        return false;
    }
    
    let cleanNumber = iti.getNumber();
    cleanNumber = cleanNumber.replace(/\s+/g, '').replace(/[^0-9+]/g, '');
    $('#phone_number').val(cleanNumber);
    $('#phone_number_err').text('');
});
      </script>
   </section>
</x-guest-layout>
