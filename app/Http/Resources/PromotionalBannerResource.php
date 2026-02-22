<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionalBannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id ?? null,
            'title' => $this->title,
            'image' => $this->getFirstMediaUrl('banner_attachment'),
            'description' => $this->description,
            'banner_type' => $this->banner_type,
            'banner_redirect_url' => $this->banner_redirect_url,
            'service_id' => $this->service_id,
            'service_name' => $this->service_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' =>  $this->reject_reason,
            'duration' => $this->duration,
            'charges' => $this->charges,
            'total_amount' => $this->total_amount,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
        ];
    }
}
