<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'latitude'   => $this->lat,
            'longitude'  => $this->long,
            'status'     => $this->status,
            'address'    => $this->address,
            'user_name'  => optional($this->user)->display_name ?? null,
        ];
    }
} 