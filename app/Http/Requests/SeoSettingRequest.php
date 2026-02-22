<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SeoSetting;

class SeoSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'meta_title' => 'required|string|max:100',
            'meta_description' => 'required|string|max:200',
            'meta_keywords' => 'required',
            'global_canonical_url' => 'required|string|max:255',
            'google_site_verification' => 'required|string|max:255',
        ];
        $seoSetting =SeoSetting::find($this->id);
        if (!$seoSetting || !$seoSetting->getFirstMediaUrl('seo_image')) {
            $rules['seo_image'] = 'required|image';
        } else {
            $rules['seo_image'] = 'nullable|image';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'seo_image.required' => 'The SEO image is required.',
            'seo_image.image' => 'The SEO image must be an image file.',
            'meta_title.required' => 'The meta title is required.',
            'meta_description.required' => 'The meta description is required.',
            'meta_keywords.required' => 'The meta keywords are required.',
            'global_canonical_url.required' => 'The global canonical URL is required.',
            'google_site_verification.required' => 'The Google site verification is required.',
        ];
    }
} 