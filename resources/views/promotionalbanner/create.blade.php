<x-master-layout>
<head>
    <!-- Other head content -->
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
</head>

<div class="container-fluid">
<div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                        <h5 class="card-title">{{ __('messages.create_promotional_banner') }}</h5>
                        <a href="{{ route('promotional-banner') }}" class="btn btn-sm btn-primary">{{ __('messages.back') }}</a>

                        </div>
                        {{-- {{ $dataTable->table(['class' => 'table  w-100'],false) }} --}}
                    </div>
                </div>
            </div>
        </div>


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <form action="{{ route('promotional-banner.store') }}" method="POST" enctype="multipart/form-data" data-toggle="validator">
                            @csrf
                            <div class="row">
                                {{-- <div class="form-group col-md-6">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required>
                                </div> --}}

                                <div class="form-group col-md-6">
                                    <div class="mb-3 text-center border border-secondary-subtle rounded p-4 bg-white shadow-sm">
                                        <label for="image" class="form-label d-block fw-bold">
                                            <img id="imagePreview" src="{{ asset('images/default.png') }}" width="200" alt="Upload Icon">
                                            <br>{{ __('messages.choose_image') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control d-none" id="image" name="image" accept=".jpg,.jpeg,.png" required>
                                        <p class="text-muted mt-2">{{ __('messages.support_formats') }}:</p>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <div class="mb-3">
                                        <label for="short_description" class="form-label fw-bold">{{ __('messages.short_description') }}</label>
                                        <div class="form-control bg-light-subtle p-3">
                                            <textarea class="border-0 w-100 bg-transparent" id="short_description"
                                                      name="short_description" maxlength="120" rows="3"
                                                      placeholder='eg. "During the service, the furniture was accidentally damaged."'
                                                      style="resize: none; outline: none;"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="date_range">{{ __('messages.date_range') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control flatpickr" id="date_range" name="date_range" placeholder="Select Date Range" required>
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="start_date" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="end_date" required>
                                </div> --}}

                                <div class="form-group col-md-6">
                                    <label for="duration">{{ __('messages.duration_days') }}</label>
                                    <input type="number" class="form-control" name="duration" id="duration" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="per_day_charge">
                                        {{ __('messages.per_day_charge') }} ({{ str_replace('0.00', '', getPriceFormat(0)) }})
                                    </label>
                                    <input type="number" class="form-control" id="per_day_charge" value="{{ $per_day_charge }}" disabled>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="total_amount">
                                        {{ __('messages.total_amount') }} ({{ str_replace('0.00', '', getPriceFormat(0)) }})
                                    </label>
                                    <input type="number" class="form-control" id="total_amount" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="banner_type">{{ __('messages.select_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="banner_type" id="banner_type" required>
                                        <option value="">{{ __('messages.select_type') }}</option>
                                        <option value="service">Service</option>
                                        <option value="link">Link</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6" id="url_field" style="display: none;">
                                    <label for="banner_redirect_url">{{ __('messages.redirect_url') }} <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" name="banner_redirect_url" id="banner_redirect_url">
                                </div>
                               @if(auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                                <div class="form-group col-md-6" id="provider_field" style="display: none;">
                                    <label for="provider_id">{{ __('messages.select_provider') }} <span class="text-danger">*</span></label>
                                    <select class="form-control select2js" name="provider_id" id="provider_id" required
                                            data-placeholder="{{ __('messages.select_provider') }}" data-ajax--url="{{ route('ajax-list', ['type' => 'provider']) }}">
                                    </select>
                                </div>
                                @else
                                    <input type="hidden" name="provider_id" id="provider_id" value="{{ auth()->user()->id }}">
                                @endif
                                <div class="form-group col-md-6" id="service_field" style="display: none;">
                                    <label class="form-label">{{ __('messages.select_service') }} <span class="text-danger">*</span></label>
                                    <select class="select2js form-group" name="service_id" id="service_id" data-placeholder="{{ __('messages.select_service') }}" data-ajax--url="{{ route('ajax-list', ['type' => 'service']) }}" >
                                    </select>
                                </div>

<div class="form-group col-md-6">
                                    <label for="payment_method">{{ __('messages.payment_method') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="payment_method" id="payment_method" required>
                                        <option value="" disabled selected>{{ __('messages.select_payment_method') }}</option>
                                        @foreach($paymentGateways as $gateway)
                                            <option value="{{ $gateway->type }}">{{ ucfirst($gateway->type) }}</option>
                                        @endforeach
                                        <!-- <option value="wallet">Wallet</option> -->
                                    </select>
                                </div>


                                {{-- <div class="form-group col-md-12">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div> --}}
                                <div class="form-group col-md-12 mt-3 text-end">
                                    <button type="submit" class="btn btn-primary">{{ __('messages.submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-master-layout>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangePicker = document.querySelector('#date_range');
    const duration = document.querySelector('input[name="duration"]');
    const perDayCharge = document.querySelector('#per_day_charge');
    const totalAmount = document.querySelector('#total_amount');
    const imageInput = document.querySelector('#image');
    const imagePreview = document.querySelector('#imagePreview');


    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const validExtensions = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validExtensions.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('messages.error') }}',
                    text: '{{ __('messages.invalid_image_format') }}',
                });
                this.value = ''; // Clear the input
                imagePreview.src = '{{ asset('images/default.png') }}'; // Reset to default image
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    flatpickr(dateRangePicker, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        minDate: 'today',
        onChange: function(selectedDates) {
            if (selectedDates.length === 2) {
                const start = selectedDates[0];
                const end = selectedDates[1];
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                duration.value = diffDays;
                totalAmount.value = diffDays * parseFloat(perDayCharge.value);
            }
        }
    });
});

$(document).ready(function() {
    $('#banner_type').change(function() {
        if($(this).val() === 'link') {
            $('#url_field').show();
            $('#service_field, #provider_field').hide();
            $('#banner_redirect_url').prop('required', true);
            $('#service_id, #provider_id').prop('required', false);
        } else if($(this).val() === 'service') {
            $('#url_field').hide();
            $('#service_field,#provider_field').show();
            $('#banner_redirect_url').prop('required', false);
            $('#service_id,provider_id').prop('required', true);
        }
    });

    // Initialize select2
    function loadServices(providerId) {
        $('#service_id').empty().trigger('change');

        if (providerId) {
            $('#service_id').select2({
                width: '100%',
                ajax: {
                    url: '{{ route("ajax-list", ["type" => "service"]) }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            provider_id: providerId, // selected provider
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    }
                }
            });
        }
    }

    @if(auth()->user()->hasRole('provider'))
        let providerId = $('#provider_id').val();
        loadServices(providerId);
    @endif

    $('#provider_id').change(function() {
        let providerId = $(this).val();
        loadServices(providerId);
    });


    $(document).ready(function() {
    $('#payment_method').change(function() {
        const method = $(this).val();
        const paymentDetails = $('#payment_details');

        if (method) {
            paymentDetails.show();
            // Load payment gateway specific fields based on selection
            switch(method) {
                case 'stripe':
                    // Add Stripe specific fields
                    break;
                case 'paypal':
                    // Add PayPal specific fields
                    break;
                case 'razorpay':
                    // Razorpay specific fields
                    $('#razorpay_payment_details').show(); // This should be the container to input Razorpay-specific details
                    break;
                case 'flutterwave':
                    // Add Flutterwave specific fields
                    break;
                case 'wallet':
                    // Add Wallet specific fields
                    break;
                // Add other payment methods
            }
        } else {
            paymentDetails.hide();
        }
    });

    $('form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const formData = new FormData(this);

        // Disable submit button and show loading
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> {{ __('messages.processing') }}');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log(response);

                if (response.payment_method == 'razorPay') {
                    var options = {
                            "key": response.key,
                            "amount": response.amount,
                            "currency": response.currency,
                            "name": response.name,

                            "order_id": response.order_id,
                            "handler": function (paymentResponse){
                                const successUrl = new URL(response.success_url);
                                successUrl.searchParams.append('gateway', 'razorpay');
                                successUrl.searchParams.append('razorpay_payment_id', paymentResponse.razorpay_payment_id);
                                successUrl.searchParams.append('banner_id', response.banner_id);

                                window.location.href = successUrl.toString();
                            },
                            "prefill": {
                                "name": response.prefill.name??'-',
                                "email": response.prefill.email,
                                "contact": response.prefill.contact??'-',
                            },
                            "theme": {
                                "color": "#F37254"
                            }
                        };
                        console.log("Razorpay Options:", options);
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                }

                if (response.data && response.data.payment_method == 'flutterwave') {
                    const config = response.data;
                    console.log(response.data,config);
                FlutterwaveCheckout({
                    public_key: config.public_key,
                    tx_ref: config.tx_ref,
                    amount: config.amount,
                    currency: config.currency,
                    payment_options: config.payment_options,
                    customer: {
                        email: config.customer.email,
                        name: config.customer.name,
                        phone_number: config.customer.phonenumber
                    },
                    customizations: config.customizations,
                    callback: function(response) {
                        if (response.status === "successful") {
                            window.location.href = config.redirect_url +
                                '&transaction_id=' + response.transaction_id +
                                '&tx_ref=' + response.tx_ref +
                                '&plan_id=' + config.meta.banner_id;
                        } else {
                            alert('Payment failed. Please try again.');
                        }
                    },
                    onclose: function() {
                        // Handle when customer closes the payment modal
                    }
                });

                }

                if (response.status && response.checkout_url) {
                    // Redirect to Stripe checkout

                    window.location.href = response.checkout_url;
                } else if (response.status && response.message) {
                    // Success for wallet payment
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('messages.success') }}',
                        text: response.message,
                    }).then(() => {
                        console.log(response);
                        window.location.href = '{{ route("promotional-banner") }}';
                    });
                }
            },
            error: function(xhr) {
                // Reset button state
                submitBtn.prop('disabled', false).text('{{ __('messages.submit') }}');

                // Handle validation errors
                if (xhr.status === 422) {
                    console.log(1);
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(field) {
                        const input = form.find(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message === 'Insufficient balance') {
                    // Show insufficient balance error
                    console.log(2);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('messages.error') }}',
                        text: '{{ __('messages.insufficient_balance') }}',
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    console.log(3);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('messages.error') }}',
                        text: xhr.responseJSON.message,
                    });
                } else {

                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('messages.error') }}',
                        text: xhr.responseJSON.error,
                    });
                }
            }
        });
    });

    // Clear validation errors when input changes
    $('form input, form select').on('input change', function() {
        $(this).removeClass('is-invalid')
            .next('.invalid-feedback').remove();
    });
});
});
</script>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script>
@endpush
