<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;

class UserFavouriteResource extends JsonResource
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
        $user_id = auth()->user() ? (request()->customer_id ?? auth()->user()->id) : null;
        return [
            'id'            => $this->id,
            'service_id'    => $this->service_id,
            'user_id'       => $this->user_id,
            'created_at'    => date('Y-m-d', strtotime($this->created_at)),
            'customer_name' => optional($this->customer)->display_name,
            'name'          => $this->getTranslation(optional($this->service)->translations, $headerValue, 'name', optional($this->service)->name) ?? optional($this->service)->name,
            'description'   => $this->getTranslation(optional($this->service)->translations, $headerValue, 'name', optional($this->service)->description) ?? optional($this->service)->description,
            'price'         => optional($this->service)->price,
            'price_format'  => getPriceFormat(optional($this->service)->price),
            'type'          => optional($this->service)->type,
            'discount'      => optional($this->service)->discount,
            'duration'      => optional($this->service)->duration,
            'service_attchments' => getAttachments(optional($this->service)->getMedia('service_attachment'),null),
            'is_favourite'  => $this->service->getUserFavouriteService->where('user_id',$user_id)->first() ? 1 : 0,
            'total_rating'  => count($this->service->serviceRating) > 0 ? (float) number_format(max($this->service->serviceRating->avg('rating'),0), 2) : 0,
            'category_name' => $this->getTranslation(optional(optional($this->service)->category)->translations, $headerValue, 'name', optional(optional($this->service)->category)->description) ?? optional($this->service->category)->name,
            'category_id'   => $this->service->category_id,
            'provider_image'=> optional($this->service->providers)->login_type != null ? optional($this->service->providers)->social_image : getSingleMedia(optional($this->service->providers), 'profile_image',null),
            'provider_name' => optional($this->service->providers)->display_name,
            'provider_id' => optional($this->service->providers)->id
        ];
    }
}
