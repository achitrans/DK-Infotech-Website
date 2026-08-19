<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
class MarkAttendanceForAllUsers extends Command
{
    protected $signature = 'attendance:mark-missing';
    protected $description = 'Mark attendance for users who have not marked in or out for today, and set working hours to zero';

    public function handle()
    {
        $today = Carbon::today();
        if ($today->isSunday()) {
            $this->info('Today is Sunday (holiday). Attendance marking skipped.');
            return;
        }

        $settingInTimeVal = Setting::where('name', 'attendance_in_time')->value('value') ?? '9:00';
        $settingOutTimeVal = Setting::where('name', 'attendance_out_time')->value('value') ?? '17:00';
        $settingIn = Carbon::parse($settingInTimeVal);
        $settingOut = Carbon::parse($settingOutTimeVal);

        $users = User::where('type','employee')->get();
        $countIn = 0;
        $countOut = 0;
        foreach ($users as $user) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('attendance_date', $today->toDateString())
                ->first();
            if (!$attendance) {
                // Mark as absent with zero working hours
                Attendance::create([
                    'user_id' => $user->id,
                    'attendance_date' => $today->toDateString(),
                    'status' => 'absent',
                    'in_time' => null,
                    'out_time' => null,
                    'working_hours' => 0,
                    'remarks' => 'Auto marked absent',
                    'platform' => 'system',
                ]);
                $countIn++;
            } else {
                // If in_time is null, mark as absent
                if (is_null($attendance->in_time)) {
                    $attendance->status = 'absent';
                    $attendance->in_time = null;
                    $attendance->out_time = null;
                    $attendance->working_hours = 0;
                    $attendance->remarks = 'Auto marked absent';
                    $attendance->platform = 'system';
                    $attendance->save();
                    $countIn++;
                }
                // If out_time is null, mark out and set working hours to zero
                elseif (is_null($attendance->out_time)) {
                    $attendance->out_time = null;
                    $attendance->working_hours = 0;
                    $attendance->remarks = 'Auto marked out';
                    $attendance->platform = 'system';
                    $attendance->save();
                    $countOut++;
                }
                else {
                    if ($attendance->in_time && $attendance->out_time) {
                        $inTime = Carbon::parse($attendance->in_time);
                        $outTime = Carbon::parse($attendance->out_time);

                        if ($inTime->gt($settingIn) || $outTime->lt($settingOut)) {
                            $attendance->status = 'half day';
                            $attendance->save();
                        }
                    }
                }
                
            }
        }
        $this->info("Attendance marked for $countIn users (in) and $countOut users (out) for today.");
    }
}
