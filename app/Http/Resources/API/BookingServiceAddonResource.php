<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;

class BookingServiceAddonResource extends JsonResource
{
    use TranslationTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $headerValue = $headerValue = $request->header('language-code') ?? session()->get('locale', 'en');
        return [
            'id'            => $this->id,
            'name'          => $this->getTranslation(optional($this->AddonserviceDetails)->translations, $headerValue, 'name', optional($this->AddonserviceDetails)->name ?? null) ?? optional($this->AddonserviceDetails)->name,
            'service_addon_id'    => $this->service_addon_id,
            'price'         => $this->price,
            'status'        => $this->status,
           'serviceaddon_image' => getSingleMedia($this->AddonserviceDetails, 'serviceaddon_image',null),
        ];
    }
}