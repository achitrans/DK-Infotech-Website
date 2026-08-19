<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use App\Traits\GeolocationHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    use GeolocationHelpers;

    public function store(Request $request)
    {
        \Log::info("DeviceAtt", $request->all());

        if ($request->has(['latitude', 'longitude'])) {
            $check = $this->checkPerimeter($request->latitude, $request->longitude);
            if (!$check['is_within']) {
                return response()->json([
                    'status' => false,
                    'message' => $check['error'],
                    'data' => null
                ], 422);
            }
        } else {
            // Check if geometric status is ON but coordinates are missing
            $settings = $this->getGeoSettings();
            if (($settings['geo_location_status'] ?? 'off') === 'on') {
                return response()->json([
                    'status' => false,
                    'message' => 'Latitude and Longitude are required when geolocation check is enabled.',
                    'data' => null
                ], 422);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Attendance data received successfully.',
            'data' => $request->all()
        ]);
    }

    public function markIn(Request $request)
    {
        $user = Auth::user();

        // Geolocation Check
        if ($request->has(['latitude', 'longitude'])) {
            $check = $this->checkPerimeter($request->latitude, $request->longitude);
            if (!$check['is_within']) {
                return response()->json([
                    'status' => false,
                    'message' => $check['error'],
                    'data' => null
                ], 422);
            }
        } else {
            $settings = $this->getGeoSettings();
            if (($settings['geo_location_status'] ?? 'off') === 'on') {
                return response()->json([
                    'status' => false,
                    'message' => 'Location information is required to mark attendance.',
                    'data' => null
                ], 422);
            }
        }

        $now = Carbon::now();
        $inHour = (int) Setting::where('name', 'attendance_in_time')->value('value') ?: 9;
        $outHour = (int) Setting::where('name', 'attendance_out_time')->value('value') ?: 22;

        if ($now->hour < $inHour || $now->hour >= $outHour) {
            return response()->json([
                'status' => false,
                'message' => "Attendance can be marked in only between $inHour:00 and $outHour:00.",
                'data' => null
            ], 422);
        }

        $today = $now->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('attendance_date', $today)->first();

        if ($attendance) {
            return response()->json([
                'status' => false,
                'message' => 'You have already marked in today.',
                'data' => null
            ], 422);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'branch_id' => $this->branchContext->currentBranchId(),
            'attendance_date' => $today,
            'status' => 'present',
            'in_time' => $now->format('H:i:s'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'In time marked successfully.',
            'data' => $attendance
        ]);
    }

    public function markOut(Request $request)
    {
        $user = Auth::user();

        // Geolocation Check
        if ($request->has(['latitude', 'longitude'])) {
            $check = $this->checkPerimeter($request->latitude, $request->longitude);
            if (!$check['is_within']) {
                return response()->json([
                    'status' => false,
                    'message' => $check['error'],
                    'data' => null
                ], 422);
            }
        } else {
            $settings = $this->getGeoSettings();
            if (($settings['geo_location_status'] ?? 'off') === 'on') {
                return response()->json([
                    'status' => false,
                    'message' => 'Location information is required to mark attendance.',
                    'data' => null
                ], 422);
            }
        }

        $now = Carbon::now();
        $inHour = (int) Setting::where('name', 'attendance_in_time')->value('value') ?: 9;
        $outHour = (int) Setting::where('name', 'attendance_out_time')->value('value') ?: 22;

        if ($now->hour < $inHour || $now->hour >= $outHour) {
            return response()->json([
                'status' => false,
                'message' => "Attendance can be marked out only between $inHour:00 and $outHour:00.",
                'data' => null
            ], 422);
        }

        $today = $now->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('attendance_date', $today)->first();

        if (!$attendance || $attendance->out_time) {
            return response()->json([
                'status' => false,
                'message' => 'You have not marked in today or already marked out.',
                'data' => null
            ], 422);
        }

        $attendance->out_time = $now->format('H:i:s');
        $in = Carbon::parse($attendance->in_time);
        $out = Carbon::parse($attendance->out_time);
        $attendance->working_hours = $in->diffInMinutes($out) / 60;
        $attendance->save();

        return response()->json([
            'status' => true,
            'message' => 'Out time marked successfully.',
            'data' => $attendance
        ]);
    }

    public function status(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('attendance_date', $today)->first();

        if (!$attendance) {
            return response()->json([
                'status' => true,
                'message' => 'Status retrieved successfully.',
                'data' => [
                    'attendance_status' => 'not_marked'
                ]
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Status retrieved successfully.',
            'data' => [
                'attendance_status' => $attendance->out_time ? 'marked_out' : 'marked_in',
                'in_time' => $attendance->in_time,
                'out_time' => $attendance->out_time,
                'working_hours' => $attendance->working_hours,
                'attendance_date' => $attendance->attendance_date,
            ]
        ]);
    }
}
