@php
$sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
$datetime = $sitesetup ? json_decode($sitesetup->value) : null;

@endphp
{{ html()->hidden('id',$bookingdata->id ?? null) }}

<div class="card-body p-0">
    <div class="border-bottom pb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <div>
            <h3 class="mb-2 text-primary">{{__('messages.book_id')}} {{ '#' . $bookingdata->id ?? '-'}}</h3>
            <p class="opacity-75 fz-12">
                {{__('messages.book_placed')}} {{ date("$datetime->date_format $datetime->time_format", strtotime($bookingdata->created_at)) ?? '-'}}
            </p>
        </div>
        <div class="d-flex flex-wrap flex-xxl-nowrap gap-3" data-select2-id="select2-data-8-5c7s">
            <div class="w3-third">
            @if($bookingdata->handymanAdded->count() == 0 && $bookingdata->status !== "cancelled")
                @hasanyrole('admin|demo_admin|provider')
                <button class="float-end btn btn-primary" id="assign-provider" data-id="{{ $bookingdata->id }}" data-handyman-id="{{ $bookingdata->provider_id }}">
                    <i class="lab la-telegram-plane"></i>
                    {{ __('messages.assign_provider') }}
                </button>
                @endhasanyrole
                @endif
            </div>

            <div class="w3-third">
                @if($bookingdata->handymanAdded->count() == 0 && $bookingdata->status !== "cancelled")
                @hasanyrole('admin|demo_admin|provider')
                <a href="{{ route('booking.assign_form',['id'=> $bookingdata->id ]) }}"
                    class=" float-end btn btn-primary loadRemoteModel"><i class="lab la-telegram-plane"></i>
                    {{ __('messages.assign_handyman') }}</a>
                @endhasanyrole
                @endif
            </div>
            @if($bookingdata->payment_id !== null)
            <a href="{{route('invoice_pdf',$bookingdata->id)}}" class="btn btn-primary" target="_blank">
                <i class="ri-file-text-line"></i>

                {{__('messages.invoice')}}
            </a>
            @endif
        </div>
    </div>
    <div class="pay-box flex-wrap">
        <div class="pay-method-details">
            <h4 class="mb-2" style="font-size: var(--h5_fz);">{{__('messages.payment_method')}}</h4>
            @if (!empty($payment->payment_type))
            <h5 class="text-primary mb-2">{{ $payment->payment_type }}</h5>
            @else
            <h5 class="text-primary mb-2">-</h5>
            @endif
            <p><span>{{__('messages.amount')}} :
                </span><strong>{{!empty($bookingdata->total_amount) ? getPriceFormat($bookingdata->total_amount): 0}}</strong>
            </p>
        </div>
        <div class="pay-booking-details">
            <div class="d-flex justify-content-between mb-2">
                <span>{{__('messages.booking_status')}} :</span>
                <span class="text-primary" id="booking_status__span">{{ App\Models\BookingStatus::bookingStatus($bookingdata->status)}}</span>
                @if($bookingdata->status === "cancelled")
                <span class="mx-2">{{__('messages.reason')}} :</span>
                <span class="text-primary" id="booking_status__span">{{ $bookingdata->reason }}</span>
                @endif
            </div>
            
            <div class="d-flex justify-content-between gap-2 mb-2">
                <span>{{__('messages.payment_status')}} : </span>
                <span id="payment_status__span"
                    class="{{ isset($payment) && $payment->payment_status == 'paid' ? 'text-success' : 'text-danger' }}">
                    {{ isset($payment) && $payment->payment_status == 'paid' ? 'Paid' : 'Pending'}}
                </span>
            </div>
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <h5>{{__('messages.booking_date')}} :</h5>
                <span id="service_schedule__span">{{ date("$datetime->date_format $datetime->time_format", strtotime($bookingdata->date)) ?? '-'}}</span>
            </div>
        </div>
    </div>
    <div class="py-3 d-flex gap-3 flex-wrap customer-info-detail mb-2">
        <div class="bg-body rounded-3 py-3 px-4 flex-grow-1">
            <h4 class="mb-2">{{__('messages.customer_information')}}</h4>
            <h5 class="mb-3 text-primary">{{optional($bookingdata->customer)->display_name ?? '-'}}</h5>
            <ul class="list-info list-inline d-flex gap-3 flex-column">
                <li class="d-flex flex-wrap justify-content-between gap-3">
                    <span class="material-icons customer-info-text">{{__('messages.phone_information')}}</span>
                    <a href="tel:{{optional($bookingdata->customer)->contact_number}}" class="customer-info-value">
                        <p class="mb-0">{{ optional($bookingdata->customer)->contact_number ?? '-' }}</p>
                    </a>
                </li>
                <li class="d-flex flex-wrap justify-content-between gap-3">
                    <span class="material-icons  customer-info-text">{{__('messages.address')}}</span>
                    <p class="customer-info-text">{{ optional($bookingdata->customer)->address ?? '-' }}</p>
                </li>
            </ul>
        </div>

        <div class="bg-body rounded-3 py-3 px-4 flex-grow-1">
            <h4 class="mb-2">{{__('messages.provider_information')}}</h4>
            <h5 class="mb-3 text-primary">{{optional($bookingdata->provider)->display_name ?? '-'}}</h5>
            <ul class="list-info list-inline d-flex gap-3 flex-column">
                <li class="d-flex flex-wrap justify-content-between gap-3">
                    <span class="material-icons customer-info-text">{{__('messages.phone_information')}}</span>
                    <a href="tel:{{optional($bookingdata->provider)->contact_number}}" class="customer-info-value">
                        <p class="mb-0">{{ optional($bookingdata->provider)->contact_number ?? '-' }}</p>
                    </a>
                </li>
                <li class="d-flex flex-wrap justify-content-between gap-3">
                    <span class="material-icons customer-info-text">{{__('messages.address')}}</span>
                    <p class="customer-info-text">{{ optional($bookingdata->provider)->address ?? '-' }}</p>
                </li>
            </ul>
        </div>

        @if(count($bookingdata->handymanAdded) > 0)
        <div class="bg-body rounded-3 py-3 px-4 flex-grow-1">
            @foreach($bookingdata->handymanAdded as $booking)
            <h4 class="mb-2">{{__('messages.handyman_information')}}</h4>
            <h5 class="mb-3 text-primary">{{optional($booking->handyman)->display_name ?? '-'}}</h5>
            <ul class="list-info">
                <li>
                    <span class="material-icons  customer-info-text">{{__('messages.phone_information')}}</span>
                    <a href="" class=" customer-info-value">
                        <p class="mb-0">{{optional($booking->handyman)->contact_number ?? '-'}}</p>
                    </a>
                </li>
                <li>
                    <span class="material-icons  customer-info-text">{{__('messages.address')}}</span>
                    <p class=" customer-info-value mb-0">{{optional($booking->handyman)->address ?? '-'}}</p>
                </li>
            </ul>
            @endforeach
        </div>
        @endif
    </div>
    @if($bookingdata->bookingExtraCharge->count() > 0 )
    <h3 class="mb-3 mt-3">{{__('messages.extra_charge')}}</h3>
    <div class="table-responsive border-bottom">
        <table class="table text-nowrap align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-lg-3">{{__('messages.title')}}</th>
                    <th>{{__('messages.price')}}</th>
                    <th>{{__('messages.quantity')}}</th>
                    <th class="text-end">{{__('messages.total_amount')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookingdata->bookingExtraCharge as $chrage)
                <tr>
                    <td class="text-wrap ps-lg-3">
                        <div class="d-flex flex-column">
                            <a href="" class="booking-service-link fw-bold">{{$chrage->title}}</a>
                        </div>
                    </td>
                    <td>{{getPriceFormat($chrage->price)}}</td>
                    <td>{{$chrage->qty}}</td>
                    <td class="text-end">{{getPriceFormat($chrage->price * $chrage->qty)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @php
    $addonTotalPrice = 0;
    @endphp

    @if($bookingdata->bookingAddonService->count() > 0 )
    <h3 class="mb-3 mt-3">{{__('messages.service_addon')}}</h3>
    <div class="table-responsive border-bottom">
        <table class="table text-nowrap align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-lg-3">{{__('messages.title')}}</th>
                    <th>{{__('messages.price')}}</th>
                    <th class="text-end">{{__('messages.total_amount')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookingdata->bookingAddonService as $addonservice)
                @php
                $addonTotalPrice += $addonservice->price;
                @endphp
                <tr>
                    <td class="text-wrap ps-lg-3">
                        <div class="d-flex flex-column">
                            <a href="" class="booking-service-link fw-bold">{{$addonservice->name}}</a>
                        </div>
                    </td>
                    <td>{{getPriceFormat($addonservice->price)}}</td>
                    <td class="text-end">{{getPriceFormat($addonservice->price)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <h3 class="mb-3 mt-3">{{__('messages.booking_summery')}}</h3>
    <div class="table-responsive border-bottom">
        <table class="table text-nowrap align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-lg-3">{{__('messages.service')}}</th>
                    <th>{{__('messages.price')}}</th>
                    <th>{{__('messages.quantity')}}</th>
                    <th class="text-end">{{__('messages.sub_total')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-wrap ps-lg-3">
                        <div class="d-flex flex-column">
                            <a href=""
                                class="booking-service-link fw-bold">{{optional($bookingdata->service)->name ?? '-'}}</a>
                        </div>
                    </td>
                    @php
                    $serviceamount = $bookingdata->amount ? $bookingdata->amount : 0;
                    $quantity = $bookingdata->quantity ? $bookingdata->quantity : 1;

                    $amounttotal = $serviceamount * $quantity;
                    @endphp
                    <td>{{ getPriceFormat($serviceamount) }}</td>
                    <td>{{ $quantity }}</td>
                    <td class="text-end">{{ getPriceFormat($amounttotal) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row justify-content-end mt-3">
        <div class="col-sm-10 col-md-6 col-xl-5">
            <div class="table-responsive bk-summary-table">
                <table class="table-sm title-color align-right w-100">
                    <tbody>
                        <tr>
                            <td>{{ __('messages.price') }}</td>
                            @if($bookingdata->service->type == "hourly")
                            <td class="bk-value">
                                {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} / hr =
                                {{ getPriceFormat($bookingdata->final_total_service_price) }}
                            </td>
                            @else
                            <td class="bk-value">
                                {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} =
                                {{ getPriceFormat($bookingdata->amount * $bookingdata->quantity) }}
                            </td>
                            @endif
                        </tr>

                        @if($bookingdata->bookingPackage == null && $bookingdata->discount > 0)
                        <tr>
                            <td>{{ __('messages.discount') }} ({{ $bookingdata->discount }}% off)</td>
                            <td class="bk-value text-success">-{{ getPriceFormat($bookingdata->final_discount_amount) }}</td>
                        </tr>
                        @endif

                        @if($bookingdata->couponAdded != null)
                        <tr>
                            <td>{{ __('messages.coupon') }} ({{ $bookingdata->couponAdded->code }})</td>
                            <td class="bk-value text-success">-{{ getPriceFormat($bookingdata->final_coupon_discount_amount) }}</td>
                        </tr>
                        @endif

                        @php
                        // Calculate extra charges and add-ons
                        $extraCharges = $bookingdata->bookingExtraCharge->count() > 0 ? $bookingdata->getExtraChargeValue() : 0;
                        $addonTotalPrice = $bookingdata->bookingAddonService->count() > 0 ? $bookingdata->bookingAddonService->sum('price') : 0;
                        @endphp
                        @if($extraCharges > 0)
                        <tr>
                            <td>{{ __('messages.extra_charge') }}</td>
                            <td class="text-end text-success">+{{ getPriceFormat($extraCharges) }}</td>
                        </tr>
                        @endif

                        @if($addonTotalPrice > 0)
                        <tr>
                            <td>{{ __('messages.add_ons') }}</td>
                            <td class="text-end text-success">+{{ getPriceFormat($addonTotalPrice) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-sub-total">
                            <td>{{ __('messages.subtotal_vat') }}</td>
                            <td class="bk-value">{{ getPriceFormat($bookingdata->final_sub_total) ?? 0 }}</td>
                        </tr>

                        <tr>
                            <td>{{ __('messages.tax') }}</td>
                            <td class="text-end text-danger">{{ getPriceFormat($bookingdata->final_total_tax) ?? 0  }}</td>
                        </tr>

                        <tr class="grand-total">
                            <td><strong>{{ __('messages.grand_total') }}</strong></td>
                            <td class="bk-value">
                                <h3>{{ getPriceFormat($bookingdata->total_amount) ?? 0  }}</h3>
                            </td>
                        </tr>

                        @if($bookingdata->service->is_enable_advance_payment == 1)
                            <tr>
                                <td>{{ __('messages.advance_payment_amount') }} ({{ $bookingdata->service->advance_payment_amount }}%)</td>
                                <td class="text-end">{{ getPriceFormat($bookingdata->advance_paid_amount) }}</td>
                            </tr>
                           
                            <tr>
                                <td>{{ __('messages.remaining_amount') }}
                                    @if($payment != null && $payment->payment_status == 'paid')
                                    <span class="badge bg-success">{{ __('messages.paid') }}</span>
                                    @else
                                    <span class="badge bg-warning">{{ __('messages.pending') }}</span>
                                    @endif
                                </td>
                            
                                <td class="text-end">
                                    @if($payment != null && $payment->payment_status == 'paid')
                                    {{ getPriceFormat( ($bookingdata->total_amount - $bookingdata->advance_paid_amount )- $payment->total_amount) }}
                                    @else
                                    {{ getPriceFormat($bookingdata->total_amount - $bookingdata->advance_paid_amount) }}
                                    @endif

                                </td>
                            
                            </tr>
                            @if($bookingdata->status === "cancelled")
                                <tr>
                                    <td>{{ __('messages.cancellation_charge') }} ({{ $bookingdata->cancellation_charge }}%)</td>
                                    <td class="text-end">{{getPriceFormat($bookingdata->cancellation_charge_amount) ?? 0}}</td>
                                </tr>
                                @if($bookingdata->advance_paid_amount > 0)
                                    @php 
                                        $refundamount = $bookingdata->advance_paid_amount - $bookingdata->cancellation_charge_amount
                                    @endphp
                                    @if($refundamount > 0)
                                    <tr>
                                        <td>{{ __('messages.refund_amount') }}</td>
                                    
                                        <td class="text-end">{{getPriceFormat($refundamount) ?? 0}} </td>
                                    
                                    </tr>
                                    @endif
                                @endif
                            @endif
                        @endif
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>



<script>
        $(document).on('change', '.bookingstatus', function() {
            var status = $(this).val();
            var id = $(this).attr('data-id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('bookingStatus.update') }}",
                data: {
                    'status': status,
                    'bookingId': id
                },
                success: function(data) {
                    // Handle success response
                }
            });
        });

        $(document).on('change', '.paymentStatus', function() {
            var status = $(this).val();
            var id = $(this).attr('data-id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('bookingStatus.update') }}",
                data: {
                    'status': status,
                    'bookingId': id
                },
                success: function(data) {
                    // Handle success response
                }
            });
        });

        $(document).ready(function() {
            $('#assign-provider').on('click', function() {
                var bookingId = $(this).data('id');
                var handymanIds = [];
                handymanIds.push($(this).data('handyman-id'));

                // SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to assign this provider?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, assign it!',
                    cancelButtonText: 'No, cancel'
                }).then((willAssign) => {
                    if (willAssign.isConfirmed) {
                        $.ajax({
                            url: '{{ route('booking.assigned') }}',
                            type: 'POST',
                            data: {
                                id: bookingId,
                                'handyman_id[]': handymanIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire("Success!", response.message, "success");
                                window.location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire("Error!", xhr.responseText, "error");
                            }
                        });
                    } else {
                        Swal.fire("Assignment canceled!", "The provider was not assigned.", "info");
                    }
                });
            });
        });
    </script>