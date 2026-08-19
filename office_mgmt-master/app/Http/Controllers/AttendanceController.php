<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        if (Auth::user()->isEmployee()){
            $users = User::where('type', 'employee')->where('id', Auth::id())->get();
        }else{
            $users = User::where('type', 'employee')->get();
        }
        $query = Attendance::with('user');


        if (Auth::user()->isEmployee()){
            $query->where('user_id', Auth::id());
        }else{
            if ($request->filled('user_id') && $request->user_id != 'all') {
                $query->where('user_id', $request->user_id);
            }
        }

        if ($request->filled('from_date')) {
            $query->where('attendance_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('attendance_date', '<=', $request->to_date);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        return view('attendances.index', [
            'attendances' => $attendances,
            'users' => $users,
            'filters' => [
                'user_id' => $request->user_id ?? 'all',
                'from_date' => $request->from_date ?? null,
                'to_date' => $request->to_date ?? null,
            ],
        ]);
    }

    public function report(Request $request)
    {
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $users = User::where('type', 'employee')->get();
        $report = [];

        if (!$from_date || !$to_date) {
            return view('attendances.report', [
                'report' => $report,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ]);
        }

        foreach ($users as $user) {
            $attendances = Attendance::where('user_id', $user->id)
                ->when($from_date, function ($q) use ($from_date) {
                    return $q->where('attendance_date', '>=', $from_date);
                })
                ->when($to_date, function ($q) use ($to_date) {
                    return $q->where('attendance_date', '<=', $to_date);
                })
                ->get();

            $total_present = $attendances->where('status', 'present')->count();
            $total_absent = $attendances->where('status', 'absent')->count();
            $total_working_hours = $attendances->sum('working_hours');

            $report[] = [
                'user' => $user,
                'total_present' => $total_present,
                'total_absent' => $total_absent,
                'total_working_hours' => $total_working_hours,
            ];
        }

        return view('attendances.report', [
            'report' => $report,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|string|in:present,absent,half day,late,leave',
            'in_time' => 'nullable|string',
            'out_time' => 'nullable|string',
            'working_hours' => 'nullable|numeric|min:0|max:24',
            'remarks' => 'nullable|string|max:255',
        ]);

        $attendance->status = $request->status;
        $attendance->in_time = $request->in_time ?: null;
        $attendance->out_time = $request->out_time ?: null;
        if ($request->has('remarks')) {
            $attendance->remarks = $request->remarks;
        }

        if ($request->filled('working_hours')) {
            $attendance->working_hours = (float) $request->working_hours;
        } else {
            if ($attendance->status === 'absent') {
                $attendance->working_hours = 0;
            } elseif ($attendance->in_time && $attendance->out_time) {
                $in = \Carbon\Carbon::parse($attendance->in_time);
                $out = \Carbon\Carbon::parse($attendance->out_time);
                $attendance->working_hours = round($in->diffInMinutes($out) / 60, 2);
            }
        }

        $attendance->save();

        return redirect()->back()->with('success', 'Attendance record updated successfully.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendances,id',
            'status' => 'nullable|string|in:keep,present,absent,half day,late,leave',
            'in_time' => 'nullable|string',
            'out_time' => 'nullable|string',
            'working_hours' => 'nullable|numeric|min:0|max:24',
            'remarks' => 'nullable|string|max:255',
        ]);

        $attendances = Attendance::whereIn('id', $request->attendance_ids)->get();
        if ($attendances->isEmpty()) {
            return redirect()->back()->with('error', 'No valid attendance records selected.');
        }

        $updatedCount = 0;
        foreach ($attendances as $attendance) {
            if ($request->filled('status') && $request->status !== 'keep') {
                $attendance->status = $request->status;
            }
            if ($request->filled('in_time')) {
                $attendance->in_time = $request->in_time;
            }
            if ($request->filled('out_time')) {
                $attendance->out_time = $request->out_time;
            }
            if ($request->filled('remarks')) {
                $attendance->remarks = $request->remarks;
            }

            if ($request->filled('working_hours')) {
                $attendance->working_hours = (float) $request->working_hours;
            } else {
                if ($attendance->status === 'absent') {
                    $attendance->working_hours = 0;
                } elseif ($attendance->in_time && $attendance->out_time) {
                    $in = \Carbon\Carbon::parse($attendance->in_time);
                    $out = \Carbon\Carbon::parse($attendance->out_time);
                    $attendance->working_hours = round($in->diffInMinutes($out) / 60, 2);
                }
            }

            $attendance->save();
            $updatedCount++;
        }

        return redirect()->back()->with('success', "Successfully updated $updatedCount attendance record(s).");
    }
}
