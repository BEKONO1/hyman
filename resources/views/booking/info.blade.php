@php
$sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
$datetime = $sitesetup ? json_decode($sitesetup->value) : null;
@endphp
{{ html()->hidden('id',$bookingdata->id ?? null) }}
<div class="container-fluid">
    <div class="row">   
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Header Section -->
                        <div class="border-bottom pb-1 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                            <div>
                                <h3 class="mb-2 text-primary">{{__('messages.book_id')}} {{ '#' . $bookingdata->id ?? '-'}}</h3>
                            </div>
                            <div class="d-flex flex-wrap flex-xxl-nowrap gap-3">
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
                        <!-- Main Content Row -->
                        <div class="row ">    
                            <div class="col-md-4 ">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{__('messages.book_placed')}}</p>
                                        <p class="mb-0">{{ date("$datetime->date_format $datetime->time_format", strtotime($bookingdata->created_at)) ?? '-'}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{__('messages.booking_date')}}</p>
                                        <p class="mb-0" id="service_schedule__span">{{ date("$datetime->date_format $datetime->time_format", strtotime($bookingdata->date)) ?? '-'}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{__('messages.booking_status')}}</p>
                                        <p class="mb-0 text-primary" id="booking_status__span">{{ App\Models\BookingStatus::bookingStatus($bookingdata->status)}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{__('messages.total_amount')}}</p>
                                        <p class="mb-0 text-primary">{{ getPriceFormat($bookingdata->total_amount) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{__('messages.payment_method')}}</p>
                                        <p class="mb-0 text-primary">{{ isset($payment) ? ucfirst($payment->payment_type) : '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{ __('messages.payment_status') }}</p>
                                        @if(isset($payment) && $payment->payment_status)
                                            @php
                                                $statusClass = match($payment->payment_status) {
                                                    'paid', 'advanced_paid' => 'text-success',
                                                    'Advanced Refund' => 'text-warning',
                                                    default => 'text-danger',
                                                };
                                            @endphp
                                            <p class="mb-0 {{ $statusClass }}">
                                                {{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}
                                            </p>
                                        @else
                                            <p class="mb-0 text-danger">
                                                {{ __('messages.pending') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Add Cancellation Reason Card -->
                            @if($bookingdata->status === 'cancelled')
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="opacity-75 fz-12">{{ __('landingpage.cancel_reason') }}</p>
                                        <p class="mb-0 text-danger">
                                            {{ $bookingdata->reason ?? __('messages.no_reason_provided') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>  

            <!-- Order information section  -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    
                                                        <img src="{{ getSingleMedia($bookingdata->customer,'profile_image', null) }}" 
                                                            alt="Customer Profile" 
                                                            class="rounded-circle"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                            @if(optional($bookingdata->customer)->profile_image)
                                                        <img src="{{asset('public/images/default.png')}}" 
                                                            alt="Default Profile" 
                                                            class="rounded-circle"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                <p class="mb-1 text-primary">{{__('messages.customer')}}</p>
                                <h5 class="mb-2">{{optional($bookingdata->customer)->display_name ?? '-'}}</h5>
                            </div>
                        </div>
                                <ul class="list-unstyled mt-3">
                                    <li class="d-flex align-items-center mb-2">
                                        <i class="ri-phone-line me-2"></i>
                                        <a href="tel:{{optional($bookingdata->customer)->contact_number}}" class="text-body">
                                            {{ optional($bookingdata->customer)->contact_number ?? '-' }}
                                        </a>
                                    </li>
                                    <!-- <li class="d-flex align-items-center mb-2">
                                        <i class="ri-mail-line me-2"></i>
                                        <a href="mailto:{{optional($bookingdata->customer)->email}}" class="text-body">
                                            {{ optional($bookingdata->customer)->email ?? '-' }}
                                        </a>
                                    </li> -->
                                    <li class="d-flex align-items-center">
                                        <i class="ri-map-pin-line me-2"></i>
                                        <span class="text-wrap">{{ optional($bookingdata->customer)->address ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                    </div>
                </div>
                <!-- Provider Information -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    
                                        <img src="{{ getSingleMedia($bookingdata->provider,'profile_image', null) }}" 
                                            alt="Provider Profile" 
                                            class="rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @if(optional($bookingdata->provider)->profile_image)
                                        <img src="{{ asset('images/default-user.png') }}" 
                                            alt="Default Profile" 
                                            class="rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 text-primary">
                                        {{ __('messages.provider') }} 
                                        @if($bookingdata->handymanAdded->isNotEmpty() && $bookingdata->provider->id == optional($bookingdata->handymanAdded->first())->handyman->id)
                                            <span class="text-muted"> ({{ __('landingpage.as_handyman') }})</span>
                                        @endif
                                    </p>
                                    <h5 class="mb-2">{{optional($bookingdata->provider)->display_name ?? '-'}}</h5>
                                </div>
                            </div>
                                    <ul class="list-unstyled mt-3">
                                        <li class="d-flex align-items-center mb-2">
                                            <i class="ri-phone-line me-2"></i>
                                            <a href="tel:{{optional($bookingdata->provider)->contact_number}}" class="text-body">
                                                {{ optional($bookingdata->provider)->contact_number ?? '-' }}
                                            </a>
                                        </li>
                                        <!-- <li class="d-flex align-items-center mb-2">
                                            <i class="ri-mail-line me-2"></i>
                                            <a href="mailto:{{optional($bookingdata->provider)->email}}" class="text-body">
                                                {{ optional($bookingdata->provider)->email ?? '-' }}
                                            </a>
                                        </li> -->
                                        <li class="d-flex align-items-center">
                                            <i class="ri-map-pin-line me-2"></i>
                                            <span class="text-wrap">{{ optional($bookingdata->provider)->address ?? '-' }}</span>
                                        </li>
                                    </ul>
                        </div>
                    </div>
                </div>
                <!-- Handyman Information -->
                <div class="col-md-4">
                    <div class="card">
                        @if(count($bookingdata->handymanAdded) > 0)
                            @foreach($bookingdata->handymanAdded as $booking)
                                @if(optional($bookingdata->provider)->id !== optional($booking->handyman)->id)
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-4">
                                            <div class="flex-shrink-0">

                                                <img src="{{ getSingleMedia($booking->handyman,'profile_image', null) }}" 
                                                    alt="Handyman Profile" 
                                                    class="rounded-circle"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                @if(optional($booking->handyman)->profile_image)
                                                    <img src="{{ asset('images/default-user.png') }}" 
                                                        alt="Default Profile" 
                                                        class="rounded-circle"
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 text-primary">{{__('messages.handyman')}}</p>
                                                <h5 class="mb-2 ">{{optional($booking->handyman)->display_name ?? '-'}}</h5>
                                            </div>
                                        </div>
                                        <ul class="list-unstyled mt-3">
                                            <li class="d-flex align-items-center mb-2">
                                                <i class="ri-phone-line me-2"></i>
                                                <a href="tel:{{optional($booking->handyman)->contact_number}}" class="text-body">
                                                    {{ optional($booking->handyman)->contact_number ?? '-' }}
                                                </a>
                                            </li>
                                            <li class="d-flex align-items-center">
                                                <i class="ri-map-pin-line me-2"></i>
                                                <span class="text-wrap">{{ optional($booking->handyman)->address ?? '-' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
                    
            </div>  
        </div>  

        <!-- billing section -->
        <div class="col-lg-4 mt-lg-0 mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                                    <table class="table table-borderless booking-info-table align-middle mb-0">
                                        <tbody>
                                            <h3 class="mb-3">{{__('messages.payment_summary')}}</h3>
                                            <tr class="border-bottom">
                                                <td>{{__('messages.quantity')}}</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end">{{ $bookingdata->quantity }}</td>
                                            </tr>

                                            @if($bookingdata->service->type == "hourly")
                                            <tr class="border-bottom">
                                                <td>{{__('landingpage.service_total_time')}}</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end">
                                                    @php
                                                        $formattedDuration = convertToHoursMins($bookingdata->duration_diff)
                                                    @endphp
                                                    {{ $formattedDuration }}
                                                </td>
                                            </tr>
                                            @endif

                                            <tr class="border-bottom">
                                                <td>{{__('messages.price')}}</td>
                                                <td></td>
                                                <td></td>
                                                @if($bookingdata->service->type == "hourly")
                                                <td class="text-end">
                                                    {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} / hr =
                                                    {{ getPriceFormat($bookingdata->final_total_service_price) }}
                                                </td>
                                                @else
                                                <td class="text-end">
                                                    {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} =
                                                    {{ getPriceFormat($bookingdata->amount * $bookingdata->quantity) }}
                                                </td>
                                                @endif
                                                <!-- <td class="text-end">{{ getPriceFormat($bookingdata->amount) }} × {{ $bookingdata->quantity }} = {{ getPriceFormat($bookingdata->amount * $bookingdata->quantity) }}</td> -->
                                            </tr>

                                            <!-- discount -->
                                            @if($bookingdata->bookingPackage == null && $bookingdata->discount > 0)
                                           
                                            <tr class="border-bottom">
                                                <td colspan="3">{{ __('messages.discount') }} ({{ $bookingdata->discount }}% off)</td>
                                                <td class="text-end text-success">-{{ getPriceFormat($bookingdata->final_discount_amount) }}</td>
                                            </tr>
                                            @endif

                                            <!-- Coupon -->
                                            @if($bookingdata->couponAdded != null)
                                            <tr class="border-bottom">
                                                <td colspan="3">{{__('messages.coupon')}} ({{$bookingdata->couponAdded->code}})</td>
                                                <td class="text-end text-success">-{{ getPriceFormat($bookingdata->final_coupon_discount_amount) }}</td>
                                            </tr>
                                            @endif
                                            <!-- Extra Charges -->

                                            @php
                                            // Calculate extra charges and add-ons
                                            $extraCharges = $bookingdata->bookingExtraCharge->count() > 0 ? $bookingdata->getExtraChargeValue() : 0;
                                            $addonTotalPrice = $bookingdata->bookingAddonService->count() > 0 ? $bookingdata->bookingAddonService->sum('price') : 0;
                                            @endphp
                                            @if($extraCharges > 0)
                                            <tr class="border-bottom">
                                                <td colspan="3">{{ __('messages.extra_charge') }}</td>
                                                <td class="text-end text-success">+{{ getPriceFormat($extraCharges) }}</td>
                                            </tr>
                                            @endif
                                           
                                            @if($addonTotalPrice > 0)
                                            <tr class="border-bottom">
                                                <td colspan="3">{{ __('messages.add_ons') }}</td>
                                                <td class="text-end text-success">+{{ getPriceFormat($addonTotalPrice) }}</td>
                                            </tr>
                                            @endif
                                           

                                            @if($bookingdata->post_request_id == null)
                                            <!-- Subtotal -->
                                           
                                            <tr class="border-bottom">
                                                <td colspan="3">{{ __('messages.subtotal_vat') }}</td>
                                                <td class="text-end text-success">{{ getPriceFormat($bookingdata->final_sub_total) ?? 0 }}</td>
                                            </tr>
                                            <!-- Tax -->
                                            <tr class="border-bottom">
                                                <td colspan="3">{{__('messages.tax')}}</td>
                                                <td class="text-end text-danger">{{ getPriceFormat($bookingdata->final_total_tax) ?? 0 }}</td>
                                            </tr>
                                            @else
                                            <!-- Subtotal -->
                                           
                                            <tr class="border-bottom">
                                                <td colspan="3">{{ __('messages.subtotal_vat') }}</td>
                                                <td class="text-end text-success">{{ getPriceFormat($bookingdata->total_amount) ?? 0 }}</td>
                                            </tr>
                                           
                                            @endif
                                            <!-- Grand Total -->
                                            <tr class="border-bottom">
                                                <td colspan="3"><strong class="fs-3">{{__('messages.grand_total')}}</strong></td>
                                                <td class="text-end text-primary"><strong class="fs-3">{{ getPriceFormat($bookingdata->total_amount) ?? 0 }}</strong></td>
                                            </tr>

                                            <!-- Advance Payment -->
                                            @if($bookingdata->service->is_enable_advance_payment == 1)
                                                <tr class="border-bottom">
                                                    <td colspan="3">{{__('messages.advance_payment_amount')}} ({{$bookingdata->service->advance_payment_amount}}%)</td>
                                                    <td class="text-end">{{ getPriceFormat($bookingdata->advance_paid_amount) }}</td>
                                                </tr>
                                                @if($bookingdata->status !== "cancelled")
                                                    <tr>
                                                        <td colspan="3">
                                                            {{__('messages.remaining_amount')}}
                                                            @if($payment != null && $payment->payment_status !== 'paid')
                                                            <span class="badge bg-warning">{{__('messages.pending')}}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            @if($payment != null && $payment->payment_status == 'paid') 
                                                                {{ __('messages.paid') }}
                                                            @else
                                                                {{ getPriceFormat($bookingdata->total_amount - $bookingdata->advance_paid_amount) }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                @if($bookingdata->status === "cancelled")
                                                    <tr>
                                                        <td colspan="3">{{ __('messages.cancellation_charge') }} ({{ $bookingdata->cancellation_charge }}%)</td>
                                                        <td class="text-end">{{getPriceFormat($bookingdata->cancellation_charge_amount) ?? 0}}</td>
                                                    </tr>
                                                    @if($bookingdata->advance_paid_amount > 0)
                                                        @php 
                                                            $refundamount = $bookingdata->advance_paid_amount - $bookingdata->cancellation_charge_amount
                                                        @endphp
                                                        @if($refundamount > 0)
                                                        <tr>
                                                            <td colspan="3">{{ __('messages.refund_amount') }}</td>
                                                        
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
    </div>  

    <!-- Extra Charges table -->
    @if(count($bookingdata->bookingExtraCharge) > 0)
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive mb-4">
                    <h4 class="mb-3">{{__('messages.extra_charge')}}</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{__('messages.title')}}</th>
                                <th>{{__('messages.price')}}</th>
                                <th>{{__('messages.quantity')}}</th>
                                <th class="text-end">{{__('messages.total_amount')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookingdata->bookingExtraCharge as $charge)
                            <tr>
                                <td>{{$charge->title}}</td>
                                <td>{{getPriceFormat($charge->price)}}</td>
                                <td>{{$charge->qty}}</td>
                                <td class="text-end">{{getPriceFormat($charge->price * $charge->qty)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    @endif  

    <!-- Addon  Charges table -->
    @if($bookingdata->bookingAddonService->count() > 0 )
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive mb-4">
                    <h4 class="mb-3">{{__('messages.service_addon')}}</h4>
                    <table class="table table-bordered">
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
                        $addonTranslatedName = $addonservice->AddonserviceDetails->translate('name',app()->getLocale() ?? 'en');
                        @endphp
                        <tr>
                            <td class="text-wrap ps-lg-3">
                                <div class="d-flex flex-column">
                                    <a href="" class="booking-service-link fw-bold">{{ $addonTranslatedName ?: $addonservice->name }}</a>
                                </div>
                            </td>
                            <td>{{getPriceFormat($addonservice->price)}}</td>
                            <td class="text-end">{{getPriceFormat($addonservice->price)}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    @endif  
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