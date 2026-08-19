<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\UserLocation;
use Illuminate\Http\JsonResponse;

class UserLocationController extends Controller
{
    /**
     * Show user location timeline with optional date or range filters.
     *
     * Query params:
     * - user_id (required)
     * - date (Y-m-d) OR from (datetime) and/or to (datetime)
     * - per_page (optional)
     */
    public function timeline(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $query = UserLocation::where('user_id', $payload['user_id']);

        if (!empty($payload['date'])) {
            $start = Carbon::createFromFormat('Y-m-d', $payload['date'])->startOfDay()->toDateTimeString();
            $end = Carbon::createFromFormat('Y-m-d', $payload['date'])->endOfDay()->toDateTimeString();
            $query->whereBetween('recorded_at', [$start, $end]);
        } else {
            if (!empty($payload['from'])) {
                $query->where('recorded_at', '>=', $payload['from']);
            }
            if (!empty($payload['to'])) {
                $query->where('recorded_at', '<=', $payload['to']);
            }
        }

        $perPage = $payload['per_page'] ?? 100;

        $locations = $query->orderBy('recorded_at')->simplePaginate($perPage);

        return response()->json($locations);
    }

    /**
     * Store a single location point for a user.
     *
     * Expected payload:
     * - user_id, recorded_at, latitude, longitude (required)
     * - optional telemetry: accuracy_m, altitude_m, speed_mps, heading_deg
     * - optional metadata: source, device_id, ip
     */
    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'recorded_at' => ['required', 'date'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_m' => ['nullable', 'numeric'],
            'altitude_m' => ['nullable', 'numeric'],
            'speed_mps' => ['nullable', 'numeric'],
            'heading_deg' => ['nullable', 'integer', 'between:0,359'],
            'source' => ['nullable', 'string', 'max:16'],
            'device_id' => ['nullable', 'string', 'max:64'],
            'ip' => ['nullable', 'ip'],
        ]);

        $location = UserLocation::create($payload);

        return response()->json(['id' => $location->id, 'location' => $location], 201);
    }
}
