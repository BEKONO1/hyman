<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HandymanRating extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'handyman_ratings';
    protected $fillable = [
        'booking_id', 'handyman_id', 'service_id', 'customer_id', 'rating', 'review'
    ];

    protected $casts = [
        'booking_id'    => 'integer',
        'handyman_id'   => 'integer',
        'service_id'    => 'integer',
        'customer_id'   => 'integer',
        'rating'        => 'double',
    ];

    public function handyman()
    {
        return $this->belongsTo(User::class, 'handyman_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id', 'booking_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }


    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function scopeMyRating($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query;
        }

        if($user->hasRole('provider')) {
            return $query->whereHas('handyman', function($q) use ($user) {
                $q->where('provider_id', $user->id);
            });
        }

        if($user->hasRole('user')) {
            return $query->where('customer_id', $user->id);
        }

        if($user->hasRole('handyman')) {
            return $query->where('handyman_id',$user->id);
        }

        return $query;
    }
}
