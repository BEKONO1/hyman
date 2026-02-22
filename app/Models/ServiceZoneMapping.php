<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceZoneMapping extends Model
{
    protected $table = 'service_zone_mappings';
    
    protected $fillable = [
        'service_id',
        'zone_id'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function zone()
    {
        return $this->belongsTo(ServiceZone::class, 'zone_id');
    }
} 