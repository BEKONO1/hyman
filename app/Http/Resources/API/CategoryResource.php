<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;

class CategoryResource extends JsonResource
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
        $extention = imageExtention(getSingleMedia($this, 'category_image',null));
        return [
            'id'            => $this->id,
            'name'          => $this->getTranslation($this->translations, $headerValue, 'name', $this->name) ?? $this->name,
            'status'        => $this->status,
            'description'   => $this->getTranslation($this->translations, $headerValue, 'description', $this->description ?? null) ?? $this->description,
            'is_featured'   => $this->is_featured,
            'color'         => $this->color,
            'category_image'=> getSingleMedia($this, 'category_image',null),
            'category_extension' => $extention,
            'services' => $this->services->count(),
            'deleted_at'        => $this->deleted_at,
        ];
    }
}
