<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;

class SubCategoryResource extends JsonResource
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
        $extention = imageExtention(getSingleMedia($this, 'subcategory_image',null));
        return [
            'id'               => $this->id,
            'name'             => $this->getTranslation($this->translations, $headerValue, 'name', $this->name) ?? $this->name,
            'status'           => $this->status,
            'description'      => $this->getTranslation($this->translations, $headerValue, 'description', $this->description ?? null) ?? $this->description,
            'is_featured'      => $this->is_featured,
            'color'            => $this->color,
            'category_id'      => $this->category_id,
            'category_image'=> getSingleMedia($this, 'subcategory_image',null),
            'category_extension' => $extention,
            'category_name' =>  $this->getTranslation(optional($this->category)->translations, $headerValue, 'name', optional($this->category)->name ?? null) ?? optional($this->category)->name,
            'services' => $this->services->count(),
            'deleted_at' => $this->deleted_at
        ];
    }
}
