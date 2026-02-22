<?php

namespace App\Http\Resources\API;
use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\TranslationTrait;

class ServicePackageResource extends JsonResource
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
        $attachments = getAttachments($this->getMedia('package_attachment'));
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
            'id'=> $this->id,
            'name'=> $this->getTranslation($this->translations, $headerValue, 'name', $this->name) ?? $this->name,
            'provider_id' => $this->provider_id,
            'price'=> $this->price,
            'status'=> $this->status,
            'description'=> $this->getTranslation($this->translations, $headerValue, 'description', $this->description ?? null) ?? $this->description,
            'start_date'=> $this->start_at,
            'end_date'=> $this->end_at,
            'category_id'=> $this->category_id, // When package created based on Category wise//
            'subcategory_id'=> $this->subcategory_id, // When package created based on Category wise//
            'is_featured'=> $this->is_featured,
            'services'=>  ServiceResource::collection(Service::whereIn('id',$this->packageServices->pluck('service_id'))->get()),
            'attchments' =>  !empty($attachments) && count($attachments) > 0 ? $attachments : [asset('images/default.png')],
            'attchments_array' => getAttachmentArray($this->getMedia('package_attachment'),null),
            'category_name'  => $this->getTranslation(optional($this->category)->translations, $headerValue, 'name', optional($this->category)->name ?? null) ?? optional($this->category)->name,
            'subcategory_name'  => $this->getTranslation(optional($this->subcategory)->translations, $headerValue, 'name', optional($this->subcategory)->name ?? null) ?? optional($this->subcategory)->name,
            'package_type' =>$this->package_type,
            'total_price' => $this->getTotalPrice(),
            'translations' => ($finalTranslation === '[]' || !$finalTranslation) ? null : $finalTranslation,
        ];
    }
}
