<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceZone;

class ServiceZoneController extends Controller
{
    public function getZonesForDropdown(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        
        $zones = ServiceZone::where('status', 1)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return comman_custom_response([
            'data' => $zones
        ]);
    }
} 