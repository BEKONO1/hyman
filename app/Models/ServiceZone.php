<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceZone extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['name', 'coordinates', 'status'];

    protected $casts = [
        'coordinates' => 'array',
        'status' => 'boolean',
    ];

    // Many-to-Many with users
    public function users()
    {
        return $this->belongsToMany(User::class, 'service_zone_user');
    }

    // Many-to-Many with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_service_zone');
    }
        public function providers()
    {
        return $this->belongsToMany(User::class, 'provider_zone_mappings', 'zone_id', 'provider_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_zone_mappings', 'zone_id', 'service_id');
    }
}
