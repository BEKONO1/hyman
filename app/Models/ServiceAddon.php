<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TranslationTrait;

class ServiceAddon extends BaseModel implements  HasMedia
{
    use InteractsWithMedia,HasFactory,SoftDeletes;
    use TranslationTrait;
    protected $table = 'service_addons';
    protected $fillable = [
        'name', 'service_id','price','status','created_by'
    ];
    protected $casts = [
        'service_id'    => 'integer',
        'price'         => 'double',
        'status'        => 'integer',
        'created_by'    => 'integer',
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


    public function service(){
        return $this->belongsTo(Service::class,'service_id', 'id')->with('providers');
    }
    public function scopeList($query)
    {
        return $query->orderBy('deleted_at', 'asc');
    }
    public function scopeServiceAddon($query)
    {
        if(auth()->user()->hasRole('admin')) {

            return $query;
        }

        if (auth()->user()->hasRole('provider')) {
            $user = auth()->user();
            
            if ($user->user_type == 'provider') {
                $providerId = $user->id;
                return $query->whereHas('service', function ($query) use ($providerId) {
                    $query->where('provider_id', $providerId);
                });
            }
        }
        return $query;
    }
   
}