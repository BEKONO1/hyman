<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionEarning extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'commission_earnings';

    protected $fillable = ['employee_id','booking_id','commissions','user_type', 'commission_amount','commission_status', 'payment_date'];

    protected $casts = [

        'employee_id' => 'integer',
        'booking_id' => 'integer',
        'commission_amount' => 'double',
    ];

    public function getbooking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function earning()
    {
        return $this->hasMany(ProviderPayout::class, 'provider_id');
    }
}
