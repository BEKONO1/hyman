<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SeoSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'site_meta_description',
        'global_canonical_url',
        'google_site_verification',
        'seo_image',
    ];

    protected $casts = [
        'meta_keywords' => 'array', // Cast JSON to array
    ];

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('seo_image')
            ->singleFile();
    }

    /**
     * Get meta keywords as array
     */
    public function getMetaKeywordsArrayAttribute()
    {
        return is_array($this->meta_keywords) ? $this->meta_keywords : [];
    }

    /**
     * Set meta keywords from array
     */
    public function setMetaKeywordsArrayAttribute($value)
    {
        $this->meta_keywords = is_array($value) ? $value : [];
    }
}
