<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\TranslationTrait;

class Category extends BaseModel implements HasMedia
{
    use HasFactory,HasRoles,InteractsWithMedia,SoftDeletes;
    use TranslationTrait;
    protected $table = 'categories';
    protected $fillable = [
        'name', 'description', 'is_featured', 'status' , 'color', 'meta_title', 'meta_description', 'meta_keywords', 'canonical_url', 'seo_enabled', 'slug'
    ];
    protected $casts = [
        'status'    => 'integer',
        'is_featured'  => 'integer',
        'meta_keywords' => 'array',
        'seo_enabled' => 'boolean',
    ];
    public function translations()
    {
        return $this->morphMany(Translations::class, 'translatable');
    }

   public function translate($attribute, $locale = null)
    {
        
        $locale = $locale ?? app()->getLocale() ?? 'en';
        if($locale !== 'en'){
            $translation = $this->translations()
            ->where('attribute', $attribute)
            ->where('locale', $locale)
            ->value('value');

        return $translation !== null ?  $translation : '';
        }
        return $this->$attribute;
    }


    public function services(){
        return $this->hasMany(Service::class, 'category_id','id');
    }
    public function scopeList($query)
    {
        return $query->orderByRaw('deleted_at IS NULL DESC, deleted_at DESC')->orderBy('updated_at', 'desc');
    }
    
    public function serviceZones()
    {
        return $this->belongsToMany(ServiceZone::class, 'category_service_zone');
    }
    
    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_image')
            ->singleFile();
            
        $this->addMediaCollection('seo_image')
            ->singleFile();
    }
}
