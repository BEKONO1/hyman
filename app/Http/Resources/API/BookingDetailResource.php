<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\BookingStatus;
use App\Models\Setting;
use  App\Models\WalletHistory;
use App\Traits\TranslationTrait;
class BookingDetailResource extends JsonResource
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
        $serviceconfig = Setting::getValueByKey('service-configurations','service-configurations');
        $wallet = WalletHistory::where('user_id', $this->customer_id)  // Ensure correct user_id is used
                ->where('activity_type', 'wallet_refund')  // Filter by wallet_refund activity
                ->whereJsonContains('activity_data->booking_id', $this->id)  // Match booking_id in JSON field
                ->orderBy('created_at', 'desc')  // Get the most recent entry
                ->first();
        $advancepaid = 0;
        if($this->status == 'cancelled'){
            $advancepaid = $this->advance_paid_amount == null ? 0:(double) $this->advance_paid_amount;
        }
        if($this->status == 'cancelled'){
            $cacellationcharges = $this->cancellation_charge_amount ?? 0;
        }else{
            $cacellationcharges = $this->getCancellationCharges();
        }
        return [
            'id'                 => $this->id,
            'address'            => $this->address,
            'customer_id'        => $this->customer_id,
            'service_id'         => $this->service_id,
            'provider_id'        => $this->provider_id,
            'quantity'           => $this->quantity,
            'price'              => optional($this->service)->price,
            'price_format'       => getPriceFormat(optional($this->service)->price),
            'type'               => optional($this->service)->type,
            'discount'           => optional($this->service)->discount,
            'status'             => $this->status,
            'status_label'       => BookingStatus::bookingStatus($this->status),
            'description'        => $this->description,
            'reason'             => $this->reason,
            'provider_name'      => optional($this->provider)->display_name,
            'customer_name'      => optional($this->customer)->display_name,
            'service_name'       => $this->getTranslation(optional($this->service)->translations, $headerValue, 'name', optional($this->service)->name ?? null) ?? optional($this->service)->name,
            'payment_status'     => optional($this->payment)->payment_status,
            'payment_method'     => optional($this->payment)->payment_type,
            'payment_total_amount' => optional($this->payment)->total_amount,
            'payment_amount'     => $advancepaid,
            'total_review'       => $this->bookingRating->count('id'),
            'total_rating'       => count($this->bookingRating) > 0 ? (float) number_format(max($this->bookingRating->avg('rating'),0), 2) : 0,
            'date'               => $this->date,
            'booking_date'       => date("$datetime->date_format $datetime->time_format", strtotime($this->date)),
            'start_at'           => $this->start_at,
            'end_at'             => $this->end_at,
            'duration_diff'      => $this->duration_diff,
            'payment_id'         => $this->payment_id,
            'booking_address_id' => $this->booking_address_id,
            'duration_diff_hour' => ($this->service->type === 'hourly') ? convertToHoursMins($this->duration_diff) : null,
            'total_amount'       => $this->total_amount,
            'amount'             => $this->amount,
            'taxes'              => $this->getTaxData($this->tax),
            'extra_charges'         => BookingChargesResource::collection($this->bookingExtraCharge),
            'extra_charges_value'   => $extraValue,
            'booking_type'            => $this->type,
            'post_request_id'            => $this->post_request_id,
            'booking_slot' => $this->booking_slot,
            'booking_package'              => new BookingPackageResource($this->bookingPackage),
            'advance_paid_amount'  => $this->advance_paid_amount == null ? 0:(double) $this->advance_paid_amount,
            'advance_payment_amount' => optional($this->service)->advance_payment_amount == null ? 0:(bool) optional($this->service)->advance_payment_amount,
            'final_total_service_price' => $this->final_total_service_price,
            'final_total_tax'=> $this->final_total_tax,
            'final_sub_total'=> $this->final_sub_total,
            'final_discount_amount'=> $this->final_discount_amount,
            'final_coupon_discount_amount'=> $this->final_coupon_discount_amount,
            'txn_id' => optional($this->payment)->txn_id ?? null,
            'BookingAddonService' => BookingServiceAddonResource::collection($this->bookingAddonService),
            'cancellation_charge_hours' => isset($serviceconfig->cancellation_charge_hours) ?  (double)$serviceconfig->cancellation_charge_hours : 0,
            'cancellationcharges' => isset($serviceconfig->cancellation_charge_amount) ?  (double)$serviceconfig->cancellation_charge_amount : 0,
            'cancellation_charge_amount' => $cacellationcharges,
            'refund_amount' => $advancepaid > 0 ?  $advancepaid - $cacellationcharges : 0,
            'refund_status' =>  $advancepaid > 0 ? 'completed'  : null,
            // 'handyman'              => isset($this->handymanAdded) ? $this->handymanAdded : [],
            // 'handyman_image'        => getSingleMedia($this->handyman, 'profile_image', null),
        ];
    }
    private function getTaxData()
{
    $taxData = json_decode($this->tax ?? '[]', true);

    $taxData = array_map(function ($item) {
        $item['id'] = (int) $item['id'];
        $item['value'] = (float) $item['value'];
        return $item;
    }, $taxData);

    return $taxData;
}
}
