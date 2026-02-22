<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;
class TypeResource extends JsonResource
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
            'id'               => $this->id,
            'name'             => $this->getTranslation($this->translations, $headerValue, 'name', $this->name) ?? $this->name,
            'commission'       => $this->commission,
            'status'           => $this->status,
            'type'             => $this->type,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'deleted_at'        => $this->deleted_at,
            'created_by'       => $this->created_by,
            'updated_by'       => $this->updated_by,
            'translations' => ($finalTranslation === '[]' || !$finalTranslation) ? null : $finalTranslation,
        ];
    }
}
