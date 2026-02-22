<?php

namespace App\Traits;

use App\Models\ServiceZoneMapping;
use App\Models\ServiceZone;

trait ZoneTrait
{

    public function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km')
    {
        $earthRadius = $unit === 'km' ? 6371 : 3959; // Radius of the earth in km or miles

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }


    public function getNearbyZoneserviceIds($serviceId, $lat, $lng)
    {
        $zoneMappings = ServiceZoneMapping::with('zone')
            ->where('service_id', $serviceId)
            ->pluck('zone_id')
            ->toArray();

        $zones = ServiceZone::whereIn('id', $zoneMappings)->get();

        foreach ($zones as $zone) {
            if (!$zone || !$zone->coordinates) {
                continue;
            }

            $coordinates = $zone->coordinates;
            if (is_string($coordinates)) {
                $decoded = json_decode($coordinates, true);
                if (is_string($decoded)) {
                    $coordinates = json_decode($decoded, true); // handle double encoding
                } else {
                    $coordinates = $decoded;
                }
            }

            if (is_array($coordinates) && $this->pointInPolygon($lat, $lng, $coordinates)) {

                return true;
            }
        }

        return false;
    }


    private function pointInPolygon($lat, $lng, array $polygon): bool
    {

        $inside = false;
        $j = count($polygon) - 1;

        for ($i = 0; $i < count($polygon); $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $lng) != ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi + 0.00000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }


    public function getMatchingZonesByLatLng($lat, $lng)
    {
        $matchedZoneIds = [];

        $zones = ServiceZone::where('status', 1)->get();


        foreach ($zones as $zone) {
            if (!$zone || !$zone->coordinates) {
                continue;
            }

            $coordinates = $zone->coordinates;

            if (is_string($coordinates)) {
                $decoded = json_decode($coordinates, true);
                if (is_string($decoded)) {
                    $coordinates = json_decode($decoded, true); // handle double encoding
                } else {
                    $coordinates = $decoded;
                }
            }

            if (is_array($coordinates) && $this->pointInPolygondata($lat, $lng, $coordinates)) {
                $matchedZoneIds[] = $zone->id;
            }
        }

        return $matchedZoneIds;
    }


    public function pointInPolygondata($lat, $lng, array $polygon): bool
    {

        $inside = false;
        $j = count($polygon) - 1;

        for ($i = 0; $i < count($polygon); $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $lng) != ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / (($yj - $yi) ?: 0.00000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }
}
