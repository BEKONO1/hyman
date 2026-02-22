<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id',
        'banner_id',
        'datetime',
        'total_amount',
        'payment_type',
        'txn_id',
        'payment_status',
        'other_transaction_detail',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function banner()
    {
        return $this->belongsTo(PromotionalBanner::class, 'banner_id');
    }
}

