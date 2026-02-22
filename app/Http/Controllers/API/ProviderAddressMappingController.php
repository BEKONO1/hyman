<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProviderAddressMapping;
use App\Http\Resources\API\ProviderAddressMappingResource;
use App\Models\ServiceZone;
use App\Models\ProviderZoneMapping;

class ProviderAddressMappingController extends Controller
{
    public function getProviderAddressList(Request $request){

        $address = ProviderAddressMapping::with('providers')->where('status',1)->where('provider_id',$request->provider_id);
        if(auth()->user() !== null){
            if(auth()->user()->hasRole('admin')){
                if($request->has('provider_id') && isset($request->provider_id) && $request->provider_id == 0){
                    $address = ProviderAddressMapping::with('providers')->withTrashed();
                }else{
                    $address = ProviderAddressMapping::with('providers')->where('provider_id',$request->provider_id)->withTrashed();
                }
            }else if(auth()->user()->hasRole('provider')){

                $address = ProviderAddressMapping::with('providers')->where('provider_id',$request->provider_id);
            }
        }
        if($request->has('status') && isset($request->status)){
            $address->where('status',$request->status);
        }
        if(auth()->user() !== null){
            if(auth()->user()->user_type !== 'admin'){
                $address->whereHas('providers', function ($a) use ($request) {
                    $a->where('status', 1);
                });
            }
          
        }

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all' ){
                $per_page = $address->count();
            }
        }

        $address = $address->orderBy('created_at','desc')->paginate($per_page);
        $items = ProviderAddressMappingResource::collection($address);

        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];
        
        return comman_custom_response($response);
    }

    public function getProviderZones(Request $request)
    {
        // dd('hello');
        $provider_id = $request->provider_id;
        
        // $zones = ServiceZone::whereHas('providers', function($query) use ($provider_id) {
        //     $query->where('provider_zone_mappings.provider_id', $provider_id);
        // });

        $zones = ProviderZoneMapping::where('provider_id', $provider_id)->with('zone');

        $per_page = config('constant.PER_PAGE_LIMIT');
        if($request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all'){
                $per_page = $zones->count();
            }
        }

        $zones = $zones->orderBy('created_at','desc')->paginate($per_page);
        
        $response = [
            'pagination' => [
                'total_items' => $zones->total(),
                'per_page' => $zones->perPage(),
                'currentPage' => $zones->currentPage(),
                'totalPages' => $zones->lastPage(),
                'from' => $zones->firstItem(),
                'to' => $zones->lastItem(),
                'next_page' => $zones->nextPageUrl(),
                'previous_page' => $zones->previousPageUrl(),
            ],
            'data' => [
                'current_page' => $zones->currentPage(),
                'data' => $zones->map(function($zone) {
                    return [
                        'id' => $zone->zone->id,
                        'name' => $zone->zone->name
                    ];
                })->toArray()
            ]
        ];

        return comman_custom_response($response);
    }
}