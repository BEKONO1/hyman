<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\BookingStatus;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use App\Traits\TranslationTrait;
class BookingResource extends JsonResource
{
    use TranslationTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $headerValue = $request->header('language-code') ?? session()->get('locale', 'en');
        $extraValue = 0;
        if($this->bookingExtraCharge->count() > 0){
            foreach($this->bookingExtraCharge as $chrage){
                $extraValue += $chrage->price * $chrage->qty;
            }
        }
        $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        $datetime = json_decode($sitesetup->value);
        $payment = $this->payment()->orderBy('id','desc')->first();
        return [
            'id'                    => $this->id,
            'address'               => $this->address,
            'customer_id'           => $this->customer_id,
            'service_id'            => $this->service_id,
            'provider_id'           => $this->provider_id,
            'date'                  => $this->date,
            'booking_date'          => date("$datetime->date_format $datetime->time_format", strtotime($this->date)),
            'price'                 => optional($this->service)->price,
            'type'                  => optional($this->service)->type,
            'discount'              => optional($this->service)->discount,
            'status'                => $this->status,
            'status_label'          => BookingStatus::bookingStatus($this->status),
            'description'           => $this->description,
            'provider_name'         => optional($this->provider)->display_name,
            'customer_name'         => optional($this->customer)->display_name,
            'service_name'          => $this->getTranslation(optional($this->service)->translations, $headerValue, 'name', optional($this->service)->name ?? null) ?? optional($this->service)->name,
            'payment_id'            => $this->payment_id,
            'payment_status'        => $payment ? $payment->payment_status : null,
            'payment_method'        => $payment ? $payment->payment_type : null,
            'provider_name'         => optional($this->provider)->display_name ?? null,
            'customer_name'         => optional($this->customer)->display_name ?? null,
            'provider_image'        => getSingleMedia($this->provider, 'profile_image',null),
            'provider_is_verified'  => (bool) optional($this->provider)->is_verified,  
            'customer_image'        => getSingleMedia($this->customer, 'profile_image',null),
            // 'service_name'          => optional($this->service)->name ?? null,
          'handyman' => isset($this->handymanAdded)
            ? $this->handymanAdded->map(function($handymanMapping) {
                $handyman = $handymanMapping->handyman;

                if ($handyman) {
                    $handyman->handyman_image = getSingleMedia($handyman, 'profile_image', null);
                    $handyman->is_verified = $handyman->is_verified ? 1 : 0;
                }

                // Return original $handymanMapping object — same structure
                return $handymanMapping;
            })
            : [],

            'service_attchments'    => getAttachments(optional($this->service)->getMedia('service_attachment'),null),
            'duration_diff'         => $this->duration_diff,
            'booking_address_id'    => $this->booking_address_id,
            'duration_diff_hour'    => ($this->service->type === 'hourly') ? convertToHoursMins($this->duration_diff) : null,
            'taxes'                 => $this->getTaxData($this->tax),
            'quantity'              => $this->quantity,
            'coupon_data'           => isset($this->couponAdded) ? $this->couponAdded : null,
            'total_amount'          => $this->total_amount,
            'total_rating'          => (float) number_format(max(optional($this->service)->serviceRating->avg('rating'),0), 2),
            'amount'                => $this->amount,
            'extra_charges'         => BookingChargesResource::collection($this->bookingExtraCharge),
            'extra_charges_value'   => $extraValue,
            'booking_type'          => $this->type,
            'booking_slot'          => $this->booking_slot,
            'total_review'          => optional($this->service)->serviceRating->count(),
            'booking_package'       => new BookingPackageResource($this->bookingPackage),
            'advance_paid_amount'   => $this->advance_paid_amount == null ? 0:(double) $this->advance_paid_amount,
            'advance_payment_amount'=> optional($this->service)->advance_payment_amount == null ? 0:(bool) optional($this->service)->advance_payment_amount,

        ];
    }

    private function getTaxData()
    {
        $taxData = json_decode($this->tax, true);
        if (is_array($taxData)) {
            $taxData = array_map(function ($item) {
                $item['id'] = (int) $item['id'];
                $item['value'] = (float) $item['value'];
                return $item;
            }, $taxData);
        } else {           
            $taxData = []; 
        }
    
        return $taxData;
    }
}
