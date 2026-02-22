<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingAddressMapping extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'booking_address_mappings';
    protected $fillable = [ 'booking_id', 'address', 'latitude' , 'longitude'];

    protected $casts = [
        'booking_id'   => 'integer',
    ];
}
