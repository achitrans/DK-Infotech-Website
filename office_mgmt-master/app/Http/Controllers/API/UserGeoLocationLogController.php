<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserGeoLocationLog;
use App\Traits\GeolocationHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserGeoLocationLogController extends Controller
{
    use GeolocationHelpers;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $userLat = $request->latitude;
        $userLon = $request->longitude;

        $check = $this->checkPerimeter($userLat, $userLon);

        UserGeoLocationLog::create([
            'user_id' => $user->id,
            'latitude' => $userLat,
            'longitude' => $userLon,
            'accuracy' => $request->accuracy,
            'distance' => $check['distance'],
            'is_within_radius' => $check['is_within'],
            'ip_address' => $request->ip(),
            'device_info' => $request->header('User-Agent'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Geolocation logged successfully.',
            'data' => [
                'distance' => round($check['distance'], 2) . ' meters',
                'is_within_radius' => $check['is_within'],
            ]
        ]);
    }
}
