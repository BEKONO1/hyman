<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.list') }}</h5>
                            <a href="{{ $backUrl ?? route('user.index') }}" class=" float-end btn btn-sm btn-primary"><i
                                    class="fa fa-angle-double-left"></i> {{ __('messages.back') }}</a>
                            @if ($auth_user->can('user list'))
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('user.store'))->id('user')->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}
                        {{ html()->hidden('id', $customerdata->id ?? null) }}
                        {{ html()->hidden('user_type', 'user') }}
                        {{ html()->hidden('redirect_to', $backUrl ?? null) }}
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.first_name') . ' <span class="text-danger">*</span>', 'first_name')->class('form-control-label') }}
                                {{ html()->text('first_name', $customerdata->first_name)->placeholder(__('messages.first_name'))->class('form-control')->required() }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.last_name') . ' <span class="text-danger">*</span>', 'last_name')->class('form-control-label') }}
                                {{ html()->text('last_name', $customerdata->last_name)->placeholder(__('messages.last_name'))->class('form-control')->required() }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.username') . ' <span class="text-danger">*</span>', 'username')->class('form-control-label') }}
                                {{ html()->text('username', $customerdata->username)->placeholder(__('messages.username'))->class('form-control')->required() }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>
                            @if (auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.user_type') . ' <span class="text-danger">*</span>', 'user_type')->class('form-control-label') }}
                                    <select class='form-select select2js' id='user_type' name="user_type">
                                        @foreach ($roles as $value)
                                            <option value="{{ $value->name }}" data-type="{{ $value->id }}"
                                                {{ $customerdata->user_type == $value->name ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.email') . ' <span class="text-danger">*</span>', 'email')->class('form-control-label') }}
                                {{ html()->email('email', $customerdata->email)->placeholder(__('messages.email'))->class('form-control')->required()->attribute('pattern', '[^@]+@[^@]+\.[a-zA-Z]{2,}')->attribute('title', 'Please enter a valid email address') }}
                                <small class="help-block with-errors text-danger"></small>
                            </div>


                            @if (!isset($customerdata->id) || $customerdata->id == null)
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('messages.password') . ' <span class="text-danger">*</span>', 'password')->class('form-control-label') }}
                                    {{ html()->password('password')->class('form-control')->placeholder(__('messages.password'))->required()->attribute('autocomplete', 'new-password') }}
                                    <small class="help-block with-errors text-danger"></small>
                                </div>
                            @endif


                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.contact_number') . ' <span class="text-danger">*</span>', 'contact_number')->class('form-control-label') }}
                                {{ html()->text('contact_number', $customerdata->contact_number)->placeholder(__('messages.contact_number'))->class('form-control')->required() }}
                                <br>
                                <small id="contact_number-error" class="help-block with-errors text-danger"></small>
                            </div>


                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status', ['1' => __('messages.active'), '0' => __('messages.inactive')], $customerdata->status)->class('form-select select2js')->required() }}
                            </div>

                            <div class="form-group col-md-4">
                                {{ html()->label(__('messages.address'), 'address')->class('form-control-label') }}
                                {{ html()->textarea('address', $customerdata->address)->class('form-control textarea')->rows(3)->placeholder(__('messages.address')) }}
                            </div>
                        </div>

                        {{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
<script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
    var input = document.querySelector("#contact_number");

    // Initialize intlTelInput
    var iti = window.intlTelInput(input, {
        initialCountry: "in",
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
    });

    // Restrict input to only digits (0-9)
    input.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, ''); // Remove all non-digit characters
        if (this.value.length > 15) {
            this.value = this.value.substring(0, 15);
            $('#contact_number-error').text('Contact number should not exceed 15 digits').show();
        } else {
            $('#contact_number-error').text('').hide();
        }
    });

    // Format and set the number as "countrycode-nationalnumber" (e.g., 91-1234567890)
    function setFormattedContactNumber() {
        var countryCode = iti.getSelectedCountryData().dialCode;
        var nationalNumber = input.value.replace(/\s/g, ''); 
        var formattedNumber = '+' + countryCode + ' ' + nationalNumber;
        $('#contact_number').val(formattedNumber);
    }

// On form submit, validate and then format the number
    $('#user').on('submit', function(e) {
        // Validate using the raw input (before formatting)
        var isValid = iti.isValidNumber();
        if (!isValid) {
            e.preventDefault();
            $('#contact_number-error').text('Please enter a valid mobile number.').show();
        } else {
            $('#contact_number-error').hide();
            setFormattedContactNumber(); // Only format after validation passes
        }
    });
</script>
