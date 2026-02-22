<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderZoneMapping extends Model
{
    use HasFactory;

    protected $fillable = ['provider_id', 'zone_id'];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function zone()
    {
        return $this->belongsTo(ServiceZone::class, 'zone_id');
    }
}
