<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveLocation extends Model
{
    use HasFactory;
    protected $table = 'live_locations';
    protected $fillable = [
        'booking_id', 'latitude', 'longitude'
    ];

    protected $casts = [
        'latitude'=> 'double',
        'longitude'=> 'double',
        'booking_id'    => 'integer',
    ];

    public function booking(){
        return $this->belongsTo(booking::class,'booking_id','id');
    }

}
