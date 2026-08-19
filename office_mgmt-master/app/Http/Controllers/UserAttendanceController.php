<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Traits\GeolocationHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Setting;

class UserAttendanceController extends Controller
{
    use GeolocationHelpers;

    public function markIn(Request $request)
    {
        $user = Auth::user();

        // Geolocation Check
        if ($request->has(['latitude', 'longitude'])) {
            $check = $this->checkPerimeter($request->latitude, $request->longitude);
            if (!$check['is_within']) {
                return back()->with('error', $check['error']);
            }
        } else {
            $settings = $this->getGeoSettings();
            if (($settings['geo_location_status'] ?? 'off') === 'on') {
                return back()->with('error', 'Location information is required to mark attendance.');
            }
        }

        $now = Carbon::now();
        $inHour = (int) Setting::where('name', 'attendance_in_time')->value('value') ?? 9;
        $outHour = (int) Setting::where('name', 'attendance_out_time')->value('value') ?? 22;
        if ($now->hour < $inHour || $now->hour >= $outHour) {
            return back()->with('error', "Attendance can be marked in only between $inHour:00 and $outHour:00.");
        }
        $today = $now->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('attendance_date', $today)->first();
        if ($attendance) {
            return back()->with('error', 'You have already marked in today.');
        }
        Attendance::create([
            'user_id' => $user->id,
            'branch_id' => $this->branchContext->currentBranchId(),
            'attendance_date' => $today,
            'status' => 'present',
            'in_time' => $now->format('H:i:s'),
            'platform' => $this->detectPlatform($request),
        ]);
        return back()->with('success', 'In time marked successfully.');
    }

    public function markOut(Request $request)
    {
        $user = Auth::user();

        // Geolocation Check
        if ($request->has(['latitude', 'longitude'])) {
            $check = $this->checkPerimeter($request->latitude, $request->longitude);
            if (!$check['is_within']) {
                return back()->with('error', $check['error']);
            }
        } else {
            $settings = $this->getGeoSettings();
            if (($settings['geo_location_status'] ?? 'off') === 'on') {
                return back()->with('error', 'Location information is required to mark attendance.');
            }
        }

        $now = Carbon::now();
        $inHour = (int) Setting::where('name', 'attendance_in_time')->value('value') ?? 9;
        $outHour = (int) Setting::where('name', 'attendance_out_time')->value('value') ?? 22;
        if ($now->hour < $inHour || $now->hour >= $outHour) {
            return back()->with('error', "Attendance can be marked out only between $inHour:00 and $outHour:00.");
        }
        $today = $now->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('attendance_date', $today)->first();
        if (!$attendance || $attendance->out_time) {
            return back()->with('error', 'You have not marked in today or already marked out.');
        }
        $attendance->out_time = $now->format('H:i:s');
        // Calculate working hours
        $in = Carbon::parse($attendance->in_time);
        $out = Carbon::parse($attendance->out_time);
        $attendance->working_hours = $in->diffInMinutes($out) / 60;
        $attendance->save();
        return back()->with('success', 'Out time marked successfully.');
    }

    // For cron: mark out all users who have not marked out
    public function cronMarkOut()
    {
        $today = now()->toDateString();
        $attendances = Attendance::where('attendance_date', $today)
            ->whereNull('out_time')
            ->get();
        foreach ($attendances as $attendance) {
            $attendance->out_time = now()->format('H:i:s');
            $attendance->working_hours = 0;
            if (!$attendance->platform) {
                $attendance->platform = 'system';
            }
            $attendance->save();
        }
    }

    private function detectPlatform(Request $request): string
    {
        if ($request->has('platform')) {
            return $request->input('platform');
        }

        $userAgent = $request->header('User-Agent', '');
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'mobile';
        }

        return 'web';
    }
}
