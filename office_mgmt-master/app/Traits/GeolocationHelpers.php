<?php

namespace App\Traits;

use App\Models\Setting;

trait GeolocationHelpers
{
    /**
     * Get geolocation settings from the database.
     */
    protected function getGeoSettings()
    {
        return Setting::whereIn('name', [
            'latitude',
            'longitude',
            'geo_location_radius',
            'geo_location_status'
        ])->pluck('value', 'name');
    }

    /**
     * Calculate distance between two points using Haversine formula (in meters).
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $lat1 = (float)$lat1;
        $lon1 = (float)$lon1;
        $lat2 = (float)$lat2;
        $lon2 = (float)$lon2;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if a given set of coordinates is within the office radius.
     */
    protected function checkPerimeter($userLat, $userLon)
    {
        $settings = $this->getGeoSettings();

        if (($settings['geo_location_status'] ?? 'off') !== 'on') {
            return [
                'is_within' => true,
                'distance' => null,
                'error' => null
            ];
        }

        $officeLat = $settings['latitude'] ?? null;
        $officeLon = $settings['longitude'] ?? null;
        $radius = (float)($settings['geo_location_radius'] ?? 30);

        if (!$officeLat || !$officeLon) {
            return [
                'is_within' => true,
                'distance' => null,
                'error' => 'Office location not set, skipping perimeter check.'
            ];
        }

        $distance = $this->calculateDistance($officeLat, $officeLon, $userLat, $userLon);
        $isWithin = $distance <= $radius;

        return [
            'is_within' => $isWithin,
            'distance' => $distance,
            'error' => $isWithin ? null : "You are outside the allowed office perimeter (" . round($distance, 2) . "m from office)."
        ];
    }
}
