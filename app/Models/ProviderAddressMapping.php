<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProviderAddressMapping extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'provider_address_mappings';
    protected $fillable = [ 'provider_id', 'address', 'latitude' , 'longitude' ,'status' ];

    protected $casts = [
        'provider_id'   => 'integer',
        'status'        => 'integer',
    ];
    
    public function providers(){
        return $this->belongsTo(User::class, 'provider_id','id');
    }

    public function getServiceAddress(){
        return $this->hasMany(ProviderServiceAddressMapping::class, 'provider_address_id','id');
    }

    public function scopeMyAddress($query){
        $user = auth()->user();
        if($user->hasRole('admin') || $user->hasRole('demo_admin')) {
            return $query;
        }

        if($user->hasRole('provider')) {
            return $query->where('provider_id', $user->id);
        }
        
        return $query;
    }

    public function handyman(){
        return $this->hasMany(User::class, 'service_address_id','id');
    }
    public function scopeList($query)
    {
        return $query->orderByRaw('deleted_at IS NULL DESC, deleted_at DESC')->orderBy('updated_at', 'desc');
    }
    public function scopeLocationProvider($query, $latitude, $longitude, $distance, $unit = 'km')
    {
        $unit_value = $unit == 'km' ? 6371 : 3959;

        return $query->selectRaw("
            provider_id,
            ($unit_value * acos(
                cos(radians($latitude)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians($longitude)) + 
                sin(radians($latitude)) * 
                sin(radians(latitude))
            )) AS distance
        ")
        ->having('distance', '<=', $distance)
        ->orderBy('distance', 'asc');
    }
}
