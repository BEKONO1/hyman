<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TranslationTrait;

class SubCategory extends BaseModel implements HasMedia
{
    use HasFactory, InteractsWithMedia,SoftDeletes;
    use TranslationTrait;
    protected $table = 'sub_categories';
    protected $fillable = [
        'name', 'description', 'is_featured', 'status' , 'category_id', 'meta_title', 'meta_description', 'meta_keywords', 'slug', 'seo_enabled'
    ];

    protected $casts = [
        'status'    => 'integer',
        'is_featured'  => 'integer',
        'category_id'  => 'integer',
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
    // public function translate($attribute, $locale = null)
    // {
        
    //     $locale = $locale ?? app()->getLocale() ?? 'en';
    
    //     $translation = $this->translations()
    //         ->where('attribute', $attribute)
    //         ->where('locale', $locale)
    //         ->value('value');

    //     return $translation ?? ($locale === app()->getLocale() ? $this->$attribute : '');
    // }
    public function category(){
        return $this->belongsTo('App\Models\Category','category_id','id')->withTrashed();
    }
    public function services(){
        return $this->hasMany(Service::class, 'subcategory_id','id');
    }
    public function scopeList($query)
    {
        return $query->orderByRaw('deleted_at IS NULL DESC, deleted_at DESC')->orderBy('updated_at', 'desc');
    }
}
