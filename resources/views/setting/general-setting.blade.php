{{ html()->form('POST', route('generalsetting'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id',$generalsetting->id ?? null)->class('form-control')->placeholder('id') }}
{{ html()->hidden('page')->value($page)->class('form-control')->placeholder('id') }}
<div class="row">
    <div class="col-lg-6">

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.name') }}</label>
            <div class="col-sm-12">
                {{ html()->text('site_name', $generalsetting->site_name ?? '')->class('form-control')->placeholder(__('messages.site_name'))->id('site_name') }}

            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.description') }}</label>
            <div class="col-sm-12">
                {{ html()->textarea('site_description', $generalsetting->site_description ?? '')->class('form-control textarea')->rows(3)->placeholder(__('messages.site_description')) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.email') }}</label>
            <div class="col-sm-12">
                {{ html()->email('inquriy_email', $generalsetting->inquriy_email ?? '')->class('form-control')->placeholder(__('messages.inquriy_email')) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.phone') }}</label>
            <div class="col-sm-12">
                <div class="input-group phone-input-group">
                    {{ html()->text('helpline_number', $generalsetting->helpline_number ?? '')
                        ->class('form-control phone_number')
                        ->attribute('id', 'phone_number')
                        ->placeholder(__('messages.helpline_number')) }}
                    <input type="hidden" name="country_code" id="country_code" value="{{ $generalsetting->country_code ?? '' }}">
                </div>
                <small id="phone-error" class="help-block with-errors text-danger"></small>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.website') }}</label>
            <div class="col-sm-12">
                {{ html()->text('website', $generalsetting->website ?? '')->class('form-control website')->placeholder(__('messages.website')) }}
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            {{ html()->label(__('messages.country'))->for('country_id')->class('form-control-label col-sm-6') }}
            <div class="col-sm-12">
                {{ html()->select('country_id', [], $generalsetting->country_id)->class('select2js form-group country')->id('country_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.country')])) }}
            </div>

        </div>

        <div class="form-group">
            {{ html()->label(__('messages.state'))->for('state_id')->class('form-control-label col-sm-6') }}

            <div class="col-sm-12">
                {{ html()->select('state_id', [], $generalsetting->state_id)->class('select2js form-group state_id')->id('state_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.state')])) }}
            </div>
        </div>

        <div class="form-group">
            {{ html()->label(__('messages.city'))->for('city_id')->class('form-control-label col-sm-6') }}

            <div class="col-sm-12">
                {{ html()->select('city_id', [], $generalsetting->city_id)->class('select2js form-group city_id')->id('city_id')->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.city')])) }}
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.zipcode') }}</label>
            <div class="col-sm-12">
                {{ html()->text('zipcode', $generalsetting->zipcode ?? '')->class('form-control')->placeholder(__('messages.zipcode')) }}
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-6 form-control-label">{{ __('messages.address') }}</label>
            <div class="col-sm-12">
                {{ html()->textarea('address', $generalsetting->address ?? '')->class('form-control textarea')->rows(3)->placeholder(__('messages.address')) }}
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
            <div class="col-md-offset-3 col-sm-12 ">
                {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-right') }}
            </div>
        </div>
    </div>
</div>
{{ html()->form()->close() }}

<!-- Include necessary CSS and JS files -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<style>
.iti {
    width: 100%;
}
.iti__flag-container {
    overflow: visible;
}
.phone-input-group .iti--separate-dial-code .iti__selected-flag {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-right: none;
}
.phone-input-group .form-control {
    border-left: none;
}
.iti--separate-dial-code .iti__selected-dial-code {
    color: #212529;
}
</style>

<script>
$(document).ready(function() {
    // Existing country loading code
    loadCountry();
    var state_id = "{{ isset($generalsetting->state_id) ? $generalsetting->state_id : '' }}";
    var city_id = "{{ isset($generalsetting->city_id) ? $generalsetting->city_id : '' }}";

    // Initialize phone input
    var phoneInput = document.querySelector("#phone_number");
    var phoneError = document.querySelector("#phone-error");
    var countryCodeInput = document.querySelector("#country_code");
    
    // Initialize intlTelInput with India as default
    var iti = window.intlTelInput(phoneInput, {
        initialCountry: "in",
        separateDialCode: true,
        preferredCountries: ["in", "us", "gb"],
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        customContainer: "w-100",
        onlyCountries: ["in", "us", "gb", "ca", "au", "de", "fr", "it", "es", "pt", "nl", "be", "ch", "at", "dk", "se", "no", "fi", "ie", "nz"]
    });

    // If there's an existing phone number, format it
    if (phoneInput.value && phoneInput.value.startsWith('+')) {
    iti.setNumber(phoneInput.value);
}

    // Store the country code when changed
    phoneInput.addEventListener('countrychange', function() {
        var countryData = iti.getSelectedCountryData();
        countryCodeInput.value = countryData.dialCode;
        
        // Clear the input and update placeholder
        phoneInput.value = '';
        phoneInput.placeholder = intlTelInputUtils.getExampleNumber(countryData.iso2, true, intlTelInputUtils.numberFormat.NATIONAL);
        phoneError.style.display = 'none';
    });

    // Handle keypress - only allow digits
    phoneInput.addEventListener('keypress', function(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            e.preventDefault();
            return false;
        }
        return true;
    });

    // Handle paste - only allow digits
    phoneInput.addEventListener('paste', function(e) {
        e.preventDefault();
        var pastedText = (e.clipboardData || window.clipboardData).getData('text');
        var numbersOnly = pastedText.replace(/\D/g, '');
        
        // Get current cursor position
        var cursorPos = this.selectionStart;
        var textBefore = this.value.substring(0, cursorPos);
        var textAfter = this.value.substring(this.selectionEnd);
        
        // Combine the text with the pasted numbers
        this.value = textBefore + numbersOnly + textAfter;
        
        // Set cursor position after pasted text
        this.selectionStart = this.selectionEnd = cursorPos + numbersOnly.length;
        
        // Trigger input event for validation
        this.dispatchEvent(new Event('input'));
    });

    // Validate on input
    phoneInput.addEventListener('input', function(e) {
        // Remove any non-digit characters that might have been added
        var numbersOnly = this.value.replace(/\D/g, '');
        
        // Limit to 15 digits
        if (numbersOnly.length > 15) {
            numbersOnly = numbersOnly.substring(0, 15);
            phoneError.textContent = 'Phone number cannot exceed 15 digits';
            phoneError.style.display = 'block';
        } else {
            phoneError.style.display = 'none';
        }

        // Update the input value with only numbers
        this.value = numbersOnly;
    });

    // Format number on blur
    phoneInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            if (iti.isValidNumber()) {
                this.value = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
                phoneError.style.display = 'none';
            } else {
                phoneError.textContent = 'Invalid phone number for selected country';
                phoneError.style.display = 'block';
            }
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        if (!phoneInput.value.trim()) {
            return true; // Allow empty phone number
        }

        if (!iti.isValidNumber()) {
            e.preventDefault();
            phoneError.textContent = 'Please enter a valid phone number';
            phoneError.style.display = 'block';
            return false;
        }

        // Set the final formatted number and country code
        phoneInput.value = iti.getNumber(intlTelInputUtils.numberFormat.E164);
        countryCodeInput.value = iti.getSelectedCountryData().dialCode;
        return true;
    });

    // Set initial country code and placeholder for India
    countryCodeInput.value = '91'; // India's country code
    setTimeout(function() {
        phoneInput.placeholder = intlTelInputUtils.getExampleNumber('in', true, intlTelInputUtils.numberFormat.NATIONAL);
    }, 100);

    // Existing state and city code
        stateName(country_id, state_id);
        $(document).on('change', '#country_id', function() {
            var country = $(this).val();
            $('#state_id').empty();
            $('#city_id').empty();
            stateName(country, state_id);
    });
    
        $(document).on('change', '#state_id', function() {
            var state = $(this).val();
            $('#city_id').empty();
            cityName(state, city_id);
    });
});

// Keep existing loadCountry, stateName, and cityName functions
    function loadCountry() {
        var country_id = "{{ isset($generalsetting->country_id) ? $generalsetting->country_id : '' }}";
        var country_route = "{{ route('ajax-list', ['type' => 'country']) }}";
        country_route = country_route.replace('amp;', '');

        $.ajax({
            url: country_route,
            success: function(result) {
                $('#country_id').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.state')]) }}",
                    data: result.results
                });

                if (country_id != null) {
                    $("#country_id").val(country_id).trigger('change');
                }
            }
        });
    }

    function stateName(country, state = "") {
        var state_route = "{{ route('ajax-list', ['type' => 'state', 'country_id' => '']) }}" + country;
        state_route = state_route.replace('amp;', '');

        $.ajax({
            url: state_route,
            success: function(result) {
                $('#state_id').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.state')]) }}",
                    data: result.results
                });
                if (state != null || state != 0) {
                    $("#state_id").val(state).trigger('change');
                }
            }
        });
    }

    function cityName(state, city = "") {
        var city_route = "{{ route('ajax-list', ['type' => 'city', 'state_id' => '']) }}" + state;
        city_route = city_route.replace('amp;', '');

        $.ajax({
            url: city_route,
            success: function(result) {
                $('#city_id').select2({
                    width: '100%',
                    placeholder: "{{ trans('messages.select_name', ['select' => trans('messages.city')]) }}",
                    data: result.results
                });
                if (city != null || city != 0) {
                    $("#city_id").val(city).trigger('change');
                }
            }
        });
    }
</script>

