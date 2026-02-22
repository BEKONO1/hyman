<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;

class ServiceAddonResource extends JsonResource
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
        $headerValue = $request->header('language-code') ?? session()->get('locale', 'en');
        $translation = json_encode(
            $this->translations
                ->groupBy('locale')
                ->mapWithKeys(function ($items, $locale) {
                    return [
                        $locale => $items->pluck('value', 'attribute')->toArray()
                    ];
                })
            );
        $englishTranslation = [
            'name' => $this->name,
            'description' => $this->description,
        ];
            
            // Decode existing translations JSON for modification
        $translationsArray = $translation !== '[]' && $translation ? json_decode($translation, true) : [];
            
            // Merge `en` translations
        $translationsArray['en'] = $englishTranslation;
            
            // Encode back to JSON
        $finalTranslation = json_encode($translationsArray);
        return [
            'id'            => $this->id,
            'name'          => $this->getTranslation($this->translations, $headerValue, 'name', $this->name) ?? $this->name,
            'service_id'    => $this->service_id,
            'service_name'  => $this->getTranslation(optional($this->service)->translations, $headerValue, 'name', optional($this->service)->name ?? null) ?? $this->service->name,
            'price'         => $this->price,
            'status'        => $this->status,
            'serviceaddon_image' => optional($this->getMedia('serviceaddon_image')->first())->getUrl(),
            'translations' => ($finalTranslation === '[]' || !$finalTranslation) ? null : $finalTranslation,
        ];
    }
}
