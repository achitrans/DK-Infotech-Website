<?php

namespace App\Http\Controllers;


use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index()
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $monthStart = $today->copy()->startOfMonth();

        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
            ->where('attendance_date', $today)
            ->first();

        $project = ['total' => Project::count(),'assigned' => 0,'pending' => 0,'in_progress' => 0,'paused' => 0,'completed' => 0,'on_hold' => 0,];
        $taskCount = ['total' => 0,'pending' => 0,'Progress' => 0, 'completed' => 0,'hold' => 0,'cancelled' => 0,'closed' => 0,];

        $metaData = [];
        $pendingJobsCount = 0;
        $failedJobsTodayCount = 0;

        $projects = collect();
        $Tasks = collect();

        $attendanceCount = ['total' => 0,'present' => 0,'absent' => 0, 'leave' => 0,'late' => 0,'half_day' => 0,];
        $user = Auth::user();

        if ($user->isAdmin()) {
            $pendingJobsCount = DB::table('jobs')->count();
            $failedJobsTodayCount = DB::table('failed_jobs')
                ->whereDate('failed_at', Carbon::today())
                ->count();

            $projects = Project::all();
        }
        elseif ($user->isEmployee()) {

            $projects = Project::where('user_id', $user->id)->get();

            $Tasks = ProjectTask::whereIn(
                'project_id',
                $projects->pluck('id')
            )->get();

            // $attendances = Attendance::where('user_id', $user->id)->get();

            $attendances = Attendance::where('user_id', $user->id)->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)->get();
            $attendanceCount['total'] = $attendances->count();
            $attendanceCount['present'] = $attendances->where('status', 'present')->count();
            $attendanceCount['absent'] = $attendances->where('status', 'absent')->count();
            $attendanceCount['leave'] = $attendances->where('status', 'leave')->count();
            $attendanceCount['late'] = $attendances->where('status', 'late')->count();
            $attendanceCount['half_day'] = $attendances->where('status', 'half day')->count();

        }
        elseif ($user->isClient()) {
            $projects = Project::where('client_id', $user->id)->get();
            $Tasks = ProjectTask::whereIn('project_id',$projects->pluck('id'))->get();

            $metaData['client_id']['invoices'] = Invoice::where('client_id',$user->id)->count();

            $metaData['client_id']['total'] = Invoice::where('client_id',$user->id)->sum('grand_total');

            $metaData['client_id']['paid'] = InvoicePayment::whereHas('invoice',
                function ($query) use ($user) {
                    $query->where('client_id', $user->id);
                }
            )->sum('amount');

            $metaData['client_id']['due'] =$metaData['client_id']['total'] - $metaData['client_id']['paid'];
        }
        elseif ($user->isAssociate()) {

            $projects = Project::where('created_by',$user->id )->get();

            $Tasks = ProjectTask::whereIn('project_id',$projects->pluck('id'))->get();
        }
        else {
            $projects = Project::all();
            $Tasks = ProjectTask::whereIn('project_id', $projects->pluck('id'))->get();
        }

        $project['assigned'] = $projects->count();
        $project['pending'] = $projects->where('status', 'pending')->count();
        $project['in_progress'] = $projects->where('status', 'in progress')->count();
        $project['paused'] = $projects->where('status', 'paused')->count();
        $project['completed'] = $projects->where('status', 'completed')->count();
        $project['on_hold'] = $projects->where('status', 'on hold')->count();

        $taskCount['total'] = $Tasks->count();
        $taskCount['pending'] = $Tasks->where('status', 'pending')->count();
        $taskCount['Progress'] = $Tasks->where('status', 'in progress')->count();
        $taskCount['completed'] = $Tasks->where('status', 'completed')->count();
        $taskCount['hold'] = $Tasks->where('status', 'on hold')->count();
        $taskCount['cancelled'] = $Tasks->where('status', 'cancelled')->count();
        $taskCount['closed'] = $Tasks->where('status', 'closed')->count();

        $geoLocationStatus = Setting::where( 'name','geo_location_status')->value('value') ?? 'off';

        $meetingQuery = Meeting::with(['creator', 'client','project','inquiry','interview']);

        if (!$user->isAdmin()) {
            if ($user->isBranchManager()) {
                $meetingQuery->where('branch_id',$user->branch_id);
            } elseif ($user->isClient()) {

                $meetingQuery->where('client_id',$user->id);
            } else {
                $userEmail = $user->email;
                $meetingQuery->where(function ($q) use ($user,$userEmail) {

                    $q->where('created_by', $user->id)
                        ->orWhere('client_id', $user->id)
                        ->orWhereJsonContains(
                            'attendees',
                            $userEmail
                        );
                });
            }
        }

        $allMeetings = $meetingQuery->orderBy('start_time', 'asc')->get();

            $upcomingMeetings = $allMeetings->filter(function ($m) {
                return $m->start_time >= now()->subHours(2) && $m->status != 'cancelled';
            })->take(6);

        $googleTokenRecord = \App\Models\GoogleToken::first();
        $googleConnected = (bool) $googleTokenRecord;
        $googleEmail = $googleTokenRecord
            ? $googleTokenRecord->google_email
            : null;

        return view('dashboard', compact(
            'attendance',
            'today',
            'project',
            'taskCount',
            'attendanceCount',
            'geoLocationStatus',
            'metaData',
            'allMeetings',
            'upcomingMeetings',
            'googleConnected',
            'googleEmail',
            'pendingJobsCount',
            'failedJobsTodayCount'
        ));
    }
}
