<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Inter;
        }

        .column {
            float: left;
            width: 30%;
            padding: 0 10px;
        }

        .row {
            margin: 0 -5px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .card {
            padding: 16px;
            text-align: center;
            background-color: #F6F7F9;
        }
        table tr td{
            font-size: 14px;
        }
        table thead th{
            font-size: 14px;
        }
    </style>
</head>
<?php
    use App\Models\Setting;
    $settings = Setting::whereIn('type', ['site-setup', 'general-setting'])
        ->whereIn('key', ['site-setup', 'general-setting'])
        ->get()
        ->keyBy('key');

    $app = isset($settings['site-setup']) ? json_decode($settings['site-setup']->value) : null;
    $generaldata = isset($settings['general-setting']) ? json_decode($settings['general-setting']->value) : null;

    $extraValue = 0;
?>
<body>
    <div style="padding: 24px 0 0;">
        <div style="padding-bottom: 16px; margin-bottom: 16px; border-bottom:  1px solid #ccc;">
            <div style="overflow: hidden;">
                <div style="float: left; display: inline-block;">
                    <img style="height: 32px; width: 32px; "
                        src="data:image/png;base64,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">
                    <span
                        style="display: inline-block; line-height: normal; vertical-align: super; padding-left: 12px;">
                        <span style="color: #1C1F34; vertical-align: inherit;">Handyman User</span>
                    </span>
                </div>
                <div style="float:right; text-align:right;">
                    <span style="color:#6C757D;">{{ __('messages.invoice_date') }}:</span><span style="color: #1C1F34; padding-right: 60px;">
                        {{ \Carbon\Carbon::parse($bookingdata->date)->format('Y-m-d') ?? '-' }}</span>
                    <span style="color:#6C757D;">  {{ __('messages.invoice_id') }}-</span><span style="color: #5F60B9;"> {{ '#' . $bookingdata->id ?? '-'}}</span>
                </div>
            </div>
        </div>
        <div>
            <p style="color: #6C757D; margin-bottom: 16px;">Thanks, you have already completed the payment for this
                invoice</p>
        </div>
        <div style="margin-bottom: 16px;">
            <div style="overflow: hidden;">
                <div style="float: left; width: 75%; display: inline-block;">
                    <h5 style="color: #1C1F34; margin: 0;">Organization information:</h5>
                    <p style="color: #6C757D;  margin-top: 12px; margin-bottom: 0;">For any questions or support
                        regarding this invoice or our services, please contact us via phone or email</p>
                </div>
                <div style="float:left; width: 25%; text-align:right;">
                    <span style="color: #1C1F34; margin-bottom: 12px;">{{ $generaldata->inquriy_email}}</span>
                    <p style="color: #1C1F34;  margin-top: 12px; margin-bottom: 0;">{{ $generaldata->helpline_number}}</p>
                </div>
            </div>
        </div>
        {{-- PAYMENT INFORMATION --}}
        <div>
            <h5 style="color: #1C1F34; margin-top: 0;">{{__('messages.payment_info')}} :</h5>
            <div style="background: #F6F7F9; padding:8px 24px;">
                <div style="display: inline-block;">
                    <span style="color: #1C1F34;">{{__('messages.payment_method')}}::</span>
                    <span style="color: #6C757D; margin-left: 16px;">{{ isset($payment) ? ucfirst($payment->payment_type) : '-' }}</span>
                </div>
                <div style="display: inline-block; padding-left: 24px;">
                    <span style="color: #1C1F34;">{{ __('messages.payment_status') }} ::</span>
                        @if(isset($payment) && $payment->payment_status)
                            <span style="color: #219653; margin-left: 16px;" >
                                {{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}
                            </span>
                        @else
                            <span style="color: #FB2F2F; margin-left: 16px;">
                                {{ __('messages.pending') }}
                            </span>   
                        @endif
                </div>
            </div>
        </div>

        {{-- PERSON INFORMATION --}}

        <div style="padding: 16px 0;">
            <div class="row">
                @if ($bookingdata->customer)

                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.customer')}}:</h5>
                    <div class="card" style="text-align: start;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($bookingdata->customer)->display_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($bookingdata->customer)->contact_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.address') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($bookingdata->customer)->address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if ($bookingdata->provider)
                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.provider')}}:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($bookingdata->provider)->display_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($bookingdata->provider)->contact_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.address') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($bookingdata->provider)->address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if(count($bookingdata->handymanAdded) > 0)
                @foreach($bookingdata->handymanAdded as $booking)
                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.handyman')}}:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($booking->handyman)->display_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($booking->handyman)->contact_number ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">{{ __('messages.address') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($booking->handyman)->address ?? '-'}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- TABLE  --}}
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
            <tr> 
            <thead style="background: #F6F7F9;">
                <th style="padding:12px 30px; text-align: start;">{{__('messages.service')}}</th>
                <th style="padding:12px 30px; text-align: end;">{{__('messages.Price')}}</th>
                @if($bookingdata->service->type  == 'hourly')
                    <th style="padding:12px 30px; text-align: end;">{{__('messages.hour')}}</th>
                @else
                    <th style="padding:12px 30px; text-align: end;">{{__('messages.Qty')}}</th>
                @endif
                <th style="padding:12px 30px; text-align: end;">{{__('messages.amount')}}</th>
            </thead>

            </tr>
            
            <tbody>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"> {{optional($bookingdata->service)->name ?? '-'}}</td>
                    <td style="padding:12px 30px; text-align: end;">{{ isset($bookingdata->amount) ? getPriceFormat($bookingdata->amount) : 0 }}</td>
                    @if(optional($bookingdata->service)->type  == 'hourly')
                        @php
                            $duration_minutes = $bookingdata->duration_diff / 60; // Calculate duration in minutes
                            $duration_hours = $duration_minutes > 60 ? $duration_minutes / 60 : 1; // Convert to hours if duration exceeds 60 minutes
                            // Format duration into hours:minutes format
                            $formatted_duration = gmdate('H:i', round($duration_hours * 3600));
                        @endphp
                        <td style="padding:12px 30px; text-align: end;">{{!empty($formatted_duration) ? $formatted_duration     : 0}} hr</td>
                    @else
                        <td style="padding:12px 30px; text-align: end;">{{!empty($bookingdata->quantity) ? $bookingdata->quantity : 0}}</td>
                    @endif

                    @php
                    if($bookingdata->type == 'service'){
                        if($bookingdata->service->type === 'fixed'){
                            $sub_total = ($bookingdata->amount) * ($bookingdata->quantity);
                        }else{
                            $sub_total = $bookingdata->final_total_service_price;
                        }
                    }else{
                        $sub_total = $bookingdata->amount;
                    }

                 @endphp
               <td style="padding:12px 30px; text-align: end;">{{!empty($sub_total) ? getPriceFormat($sub_total) : 0}}</td>
                </tr>
            </tbody>
        </table>

        {{-- BILLING TABLE --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
            <tbody style="background: #F6F7F9;">

                {{-- PRICE --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{__('messages.Price')}}</td>
                    @if($bookingdata->service->type == "hourly")
                        <td style="padding:12px 30px; text-align: end; color: #1C1F34;">
                            {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} / hr =
                            {{ getPriceFormat($bookingdata->final_total_service_price) }}
                        </td>
                    @else
                        <td style="padding:12px 30px; text-align: end; color: #1C1F34;">
                            {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} =
                            {{ getPriceFormat($bookingdata->amount * $bookingdata->quantity) }}
                        </td>
                    @endif
                </tr>
               
                {{-- DISCOUNT --}}
                @if($bookingdata->bookingPackage == null && $bookingdata->discount > 0)
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.discount') }} ({{ $bookingdata->discount }}% off)</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-{{ getPriceFormat($bookingdata->final_discount_amount) }}</td>
                </tr>
                @endif

                 {{-- COUPON --}}
                @if($bookingdata->couponAdded != null)
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{__('messages.coupon')}} ({{$bookingdata->couponAdded->code}})</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-{{ getPriceFormat($bookingdata->final_coupon_discount_amount) }}</td>
                </tr>
                @endif
                
                 <!-- Extra Charges -->

                 @php
                 // Calculate extra charges and add-ons
                 $extraCharges = $bookingdata->bookingExtraCharge->count() > 0 ? $bookingdata->getExtraChargeValue() : 0;
                 $addonTotalPrice = $bookingdata->bookingAddonService->count() > 0 ? $bookingdata->bookingAddonService->sum('price') : 0;
                 @endphp
                 @if($extraCharges > 0)
                 <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.extra_charge') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">+{{ getPriceFormat($extraCharges) }}</td>
                </tr>
                 @endif
                
                 @if($addonTotalPrice > 0)
                 <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.add_ons') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">+{{ getPriceFormat($addonTotalPrice) }}</td>
                </tr>
                @endif
                {{--  Sub-Total --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.sub_total') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #1C1F34;">{{!empty($bookingdata->final_sub_total) ? getPriceFormat($bookingdata->final_sub_total) : 0}}</td>
                </tr>

                {{-- TAX  --}}
                @if($bookingdata->tax != "")
                    <tr>
                        <td style="padding:12px 30px; text-align: start;"></td>
                        <td style="padding:12px 30px; text-align: end;"></td>
                        <td style="padding:12px 30px; text-align: end;"></td>
                        <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{__('messages.Tax')}} <br>
                                @foreach(json_decode($bookingdata->tax) as $key => $value)
                                    @if($value->type === 'percent')
                                        <span>({{ $value->title }} {{ $value->value }}%)</span>
                                    @else
                                        <span>({{ $value->title }} {{ getPriceFormat($value->value) }})</span>
                                    @endif
                                @endforeach
                        </td>
                        <td style="padding:12px 30px; text-align: end; color: #FB2F2F;">{{!empty($bookingdata->final_total_tax) ? getPriceFormat($bookingdata->final_total_tax) : 0}}</td>
                    </tr>
                @endif

                {{-- GRAND TOTAL --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{__('messages.grand_total')}}</td>
                    <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{ getPriceFormat($bookingdata->total_amount) ?? 0 }}</td>
                </tr>

                 <!-- Advance Payment -->
                @if($bookingdata->service->is_enable_advance_payment == 1)
                         <tr>
                            <td style="padding:12px 30px; text-align: start;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{__('messages.advance_payment_amount')}} ({{$bookingdata->service->advance_payment_amount}}%)</td>
                            <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{ getPriceFormat($bookingdata->advance_paid_amount) }}</td>
                        </tr>
                    @if($bookingdata->status !== "cancelled")
                        <tr>
                            <td style="padding:12px 30px; text-align: start;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{__('messages.remaining_amount')}}
                                    @if($payment != null && $payment->payment_status !== 'paid')
                                    <span class="badge bg-warning">( {{__('messages.pending')}} )</span>
                                    @endif
                            </td>
                            <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">
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
                                <td style="padding:12px 30px; text-align: start;"></td>
                                <td style="padding:12px 30px; text-align: end;"></td>
                                <td style="padding:12px 30px; text-align: end;"></td>
                                <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{ __('messages.cancellation_charge') }} ({{ $bookingdata->cancellation_charge }}%)</td>
                                <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{getPriceFormat($bookingdata->cancellation_charge_amount) ?? 0}}</td>
                            </tr>
                        @if($bookingdata->advance_paid_amount > 0)
                            @php 
                                $refundamount = $bookingdata->advance_paid_amount - $bookingdata->cancellation_charge_amount
                            @endphp
                            @if($refundamount > 0)
                                <tr>
                                    <td style="padding:12px 30px; text-align: start;"></td>
                                    <td style="padding:12px 30px; text-align: end;"></td>
                                    <td style="padding:12px 30px; text-align: end;"></td>
                                    <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{ __('messages.refund_amount') }}</td>
                                    <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{getPriceFormat($refundamount) ?? 0}} </td>
                                
                                </tr>
                            @endif
                        @endif
                    @endif
               @endif
             
            </tbody>
        </table>
        <div class="bottom-section">
            <h4 style="margin-bottom: 8px;">{{ __('landingpage.terms_conditions') }}</h4>
            <p style="margin:8px 0; font-size: 14px;">Payment is due upon receipt. By making a booking, you agree to our service terms, including payment
                policies, warranties, and liability limitations. Cancellations within 24 hours of the service may
                incur
                a fee. Any issues with workmanship are covered under our 30-day warranty. Contact us for details at
                <a href="#" style="text-decoration: none; color: #5F60B9;">support@handyman.com.</a>
            </p>
        </div>
        <footer style="margin-top: 8px;">
            <div style="display: inline; vertical-align: middle; margin-right: 10px;">
                <h5 style="display: inline;">For more information, visit our website:</h5>
                <a href="{{$generaldata->website}}" style="color: #5F60B9;">{{ $generaldata->website}}</a>
                <h5 style="display: block; margin: 8px 0 0;">© 2024 All Rights Reserved by IQONIC Design</h5>
            </div>
        </footer>
    </div>
</body>

</html>