<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class PromotionalBanner extends Model implements HasMedia
{
    use InteractsWithMedia,SoftDeletes,HasFactory;

    protected $table = 'promotional_banners';
    protected $fillable = [
        'title',
        'description',
        'banner_type',
        'banner_redirect_url',
        'is_requested_banner',
        'status',
        'reject_reason',
        'duration',
        'charges',
        'start_date',
        'end_date',
        'total_amount',
        'payment_method',
        'payment_status',
        'service_id',
        'provider_id',
    ];
    protected $dates = ['deleted_at']; // Add this line

    public function provider(){
        return $this->belongsTo(User::class,'provider_id', 'id')->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    // public function paymentGateway()
    // {
    //     return $this->belongsTo(PaymentGateway::class);
    // }
    // public function payment()
    // {
    //     return $this->belongsTo(Payment::class);
    // }
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($banner) {
           
        });
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')
            ->singleFile();
    }
}
