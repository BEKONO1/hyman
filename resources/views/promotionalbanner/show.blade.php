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
                            <h5 class="fw-bold">{{ $pageTitle ?? __('messages.provider_promotional_banner') }}</h5>

                            <a href="{{ route('promotional-banner') }}"
                                class="btn btn-sm btn-primary">{{ __('messages.back') }}</a>

                        </div>
                        {{-- {{ $dataTable->table(['class' => 'table  w-100'],false) }} --}}
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between gy-3">
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <p><strong>{{ __('messages.provider_name') }}:
                            </strong>{{ $banner->provider->display_name ?? 'N/A' }}</p>
                        <p><strong>{{ __('messages.promotional_banner_id') }}: </strong>{{ $banner->id }}</p>
                        <p><strong>{{ __('messages.payment_method') }}: </strong>{{ $banner->payment_method }}</p>
                        <p><strong>{{ __('messages.date') }}:
                                {{ \Carbon\Carbon::parse($banner->start_date)->format('M d, Y') }} To
                                {{ \Carbon\Carbon::parse($banner->end_date)->format('M d, Y') }}</strong></p>

                        @if (!empty($banner->description))
                            <p><strong>{{ __('messages.short_description') }}: </strong> {{ $banner->description }}
                            </p>
                        @endif

                        <p><strong>{{ __('messages.type') }}: </strong> {{ ucfirst($banner->banner_type) }}</p>

                        @if (!empty($banner->reject_reason))
                            <p><strong>{{ __('messages.reject_reason') }}: </strong> {{ $banner->reject_reason }}</p>
                        @endif

                        @if ($banner->banner_type === 'service')
                            <p><strong>{{ $banner->service ? $banner->service->name : 'N/A' }}</strong></p>
                        @else
                            <p><strong>{{ __('messages.redirect_url') }}: </strong> {{ $banner->banner_redirect_url }}
                            </p>
                        @endif

                        <img src="{{ $banner->banner_image }}" alt="Banner Image" class="img-fluid h-50 w-auto">
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6">
                        <div class="mt-3 d-flex gap-3 align-items-center justify-content-end">
                            <p class="mb-0"><strong>{{ __('messages.total_amount') }}:
                                </strong>{{ getPriceFormat($banner->total_amount) }}</p>
                            @if ($banner->status == 'accepted')
                                <span
                                    class="badge badge-active text-success bg-success-subtle">{{ __('messages.accepted') }}</span>
                            @elseif($banner->status == 'rejected')
                                <span class="badge badge-danger">{{ __('messages.rejected') }}</span>
                            @else
                                <span class="badge badge-warning bg-warning">{{ __('messages.pending') }}</span>
                            @endif
                            </p>
                        </div>

                        @if (auth()->user()->hasRole('provider') && $banner->payment_status !== 'paid' && $banner->status !== 'rejected')
                            <form action="{{ route('promotional-banner.store') }}" method="POST"
                                enctype="multipart/form-data" data-toggle="validator">
                                @csrf
                                <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                <input type="hidden" name="total_amount" value="{{ $banner->total_amount }}">
                                <input type="hidden" name="payment_status" value="paid">
                                <input type="hidden" name="payment_method" value="{{ $banner->payment_method }}">
                                <div class="form-group col-md-6">
                                    <label for="payment_method">{{ __('messages.payment_method') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" name="payment_method" id="payment_method" required>
                                        <option value="" disabled selected>
                                            {{ __('messages.select_payment_method') }}</option>
                                        @foreach ($paymentGateways as $gateway)
                                            <option value="{{ $gateway->type }}">{{ ucfirst($gateway->type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Hidden Pay Now Button -->
                                {{-- <div class="col-md-6 mt-3">
                                <button id="pay-now-btn" class="btn btn-primary d-none">{{ __('messages.pay_now') }}</button>
                            </div> --}}

                                <div class="form-group col-md-12 mt-3 text-end">
                                    <button type="submit"
                                        class="btn btn-primary">{{ __('messages.pay_now') }}</button>
                                </div>
                            </form>
                        @endif


                    </div>
                    @if (auth()->user()->hasAnyRole(['admin', 'demo_admin']) && $banner->status === 'pending')
                        <div class="mt-4 d-flex gap-2 justify-content-end">
                            <button class="btn btn-light text-dark border reject-banner" data-id="{{ $banner->id }}">
                                {{ __('messages.reject_refund') }}
                            </button>
                            <button class="btn btn-primary text-white approve-banner" data-id="{{ $banner->id }}">
                                {{ __('messages.approve') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- @push('scripts') --}}
   <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Approve Banner
            $(document).on('click', '.approve-banner', function() {
                let id = $(this).data('id');

                Swal.fire({
                    icon: 'success',
                    title: `<h2 class="swal-title">{{ __('messages.approve_banner_confirmation') }}</h2>`,
                    showCancelButton: true,
                    cancelButtonText: '{{ __('messages.cancel') }}',
                    confirmButtonText: '{{ __('messages.approve') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-alert'
                    }

                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('promotional-banner') }}/${id}/status`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: 'accepted'
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        '{{ __('messages.approved') }}',
                                        '{{ __('messages.banner_approved_successfully') }}',
                                        'success'
                                    ).then(() => {
                                        // Redirect to the main promotional banner page
                                        window.location.href =
                                            "{{ route('promotional-banner') }}";
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // Reject Banner
            $(document).on('click', '.reject-banner', function() {
                let id = $(this).data('id');

                Swal.fire({
                    icon: "error",
                    title: `<h2 style="font-size: 20px; font-weight: bold; margin-bottom: 15px;">{{ __('messages.reject_banner_confirmation') }}</h2>`,
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
                    showCancelButton: true,
                    confirmButtonText: '<span style="font-size: 14px; font-weight: bold;">Reject & Refund</span>',
                    cancelButtonText: '<span style="font-size: 14px; font-weight: bold;">Cancel</span>',
                    reverseButtons: true,
                    padding: '25px', // Adds padding for a better look
                    width: '450px', // Adjusts popup width
                    customClass: {
                        popup: 'swal2-popup'
                    },
                    preConfirm: () => {
                        const reason = document.getElementById('reject-reason').value.trim();
                        if (!reason) {
                            Swal.showValidationMessage(
                                '{{ __('messages.enter_reason_for_rejection') }}');
                            return false;
                        }
                        return $.ajax({
                            url: `{{ url('promotional-banner') }}/${id}/status`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: 'rejected',
                                reject_reason: reason
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('{{ __('messages.success') }}',
                            '{{ __('messages.banner_rejected_successfully') }}', 'success');
                        location.reload();
                    }
                });
            });
        });
        $('#payment_method').change(function() {
            const method = $(this).val();
            const paymentDetails = $('#payment_details');

            if (method) {
                paymentDetails.show();
                // Load payment gateway specific fields based on selection
                switch (method) {
                    case 'stripe':
                        // Add Stripe specific fields
                        break;
                    case 'paypal':
                        // Add PayPal specific fields
                        break;
                    case 'razorpay':
                        // Razorpay specific fields
                        $('#razorpay_payment_details')
                            .show(); // This should be the container to input Razorpay-specific details
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
            const selectedMethod = $('#payment_method').val();
            console.log('Selected Payment Method:', selectedMethod); // Debugging
            const submitBtn = form.find('button[type="submit"]');
            const formData = new FormData(this);

            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status"></span> {{ __('messages.processing') }}'
            );

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
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
                            window.location.href = '{{ route('promotional-banner') }}';
                        });

                    }
                },
                error: function(xhr) {
                    // Reset button state
                    submitBtn.prop('disabled', false).text('{{ __('messages.submit') }}');

                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(field) {
                            const input = form.find(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            input.after(
                                `<div class="invalid-feedback">${errors[field][0]}</div>`);
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message ===
                        'Insufficient balance') {
                        // Show insufficient balance error
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('messages.error') }}',
                            text: '{{ __('messages.insufficient_balance') }}',
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('messages.error') }}',
                            text: xhr.responseJSON.message,
                        });
                    } else {
                        // Show general error message
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('messages.error') }}',
                            text: '{{ __('messages.an_error_occurred') }}',
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
    </script>
    {{-- @endpush --}}

</x-master-layout>
