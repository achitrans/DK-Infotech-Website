<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class SyncAttendanceFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:sync-api {--from=} {--to=}';
    protected $description = 'Sync attendance data from external API (etimeoffice.com)';

    public function handle()
    {
        $fromDate = $this->option('from') ?? now()->format('d/m/Y');
        $toDate = $this->option('to') ?? now()->format('d/m/Y');

        $url = "https://api.etimeoffice.com/api/DownloadInOutPunchData?Empcode=ALL&FromDate={$fromDate}&ToDate={$toDate}";
        $authHeader = 'Basic RGtpbmZvdGVjaDpEa2luZm90ZWNoOkRraW5mb3RlY2gyNzFAOnRydWU6';

        $this->info("Fetching attendance data from {$fromDate} to {$toDate}...");

        try {
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
            ])->get($url);

            if ($response->failed()) {
                $this->error("API request failed with status: " . $response->status());
                return 1;
            }

            $data = $response->json();

            if (empty($data['InOutPunchData'])) {
                $this->warn("No attendance data found in the response.");
                return 0;
            }

            $count = 0;
            foreach ($data['InOutPunchData'] as $punch) {
                $employeeId = $punch['Empcode'];
                $user = User::where('employee_id', $employeeId)->first();

                if (!$user) {
                    continue;
                }

                $date = Carbon::createFromFormat('d/m/Y', $punch['DateString'])->toDateString();

                $inTime = ($punch['INTime'] === '--:--') ? null : $punch['INTime'] . ':00';
                $outTime = ($punch['OUTTime'] === '--:--') ? null : $punch['OUTTime'] . ':00';

                $workingHours = 0;
                if ($inTime && $outTime) {
                    try {
                        $startTime = Carbon::parse($inTime);
                        $endTime = Carbon::parse($outTime);
                        $workingHours = $startTime->diffInMinutes($endTime) / 60;
                    } catch (\Exception $e) {}
                }

                // Determine status based on inTime and outTime / working hours
                if (!$inTime && !$outTime) {
                    $status = 'absent';
                } elseif ($inTime && !$outTime) {
                    $status = 'present';
                } else {
                    if ($workingHours >= 7.5) {
                        $status = 'present';
                    } elseif ($workingHours >= 4) {
                        $status = 'half day';
                    } else {
                        $status = 'absent';
                    }
                }

                $attendance = Attendance::where('user_id', $user->id)
                    ->where('attendance_date', $date)
                    ->first();

                if (!$attendance) {
                    $array = [
                        'user_id' => $user->id,
                        'branch_id' => $user->branch_id,
                        'attendance_date' => $date,
                        'status' => $status,
                        'in_time' => $inTime,
                        'out_time' => $outTime,
                        'working_hours' => $workingHours,
                        'remarks' => $punch['Remark'] ?? null,
                        'platform' => 'attendance machine',
                    ];

                    Attendance::create($array);

                } else {
                    // If manually marked (web/mobile), only fill in missing fields
                    $isManual = in_array($attendance->platform, ['web', 'mobile']);

                    if ($isManual) {
                        $updateData = [];
                        if (!$attendance->in_time && $inTime) {
                            $updateData['in_time'] = $inTime;
                        }
                        if (!$attendance->out_time && $outTime) {
                            $updateData['out_time'] = $outTime;
                        }

                        if (!empty($updateData)) {
                             $finalIn = $updateData['in_time'] ?? $attendance->in_time;
                             $finalOut = $updateData['out_time'] ?? $attendance->out_time;
                             if ($finalIn && $finalOut) {
                                 $startTime = Carbon::parse($finalIn);
                                 $endTime = Carbon::parse($finalOut);
                                 $updateData['working_hours'] = $startTime->diffInMinutes($endTime) / 60;
                             }

                             if ($attendance->status === 'absent' && in_array($status, ['present', 'half day'])) {
                                 $updateData['status'] = $status;
                             }
                             $attendance->update($updateData);
                        }
                    } else {
                        $attendance->update([
                            'status' => $status,
                            'in_time' => $inTime,
                            'out_time' => $outTime,
                            'working_hours' => $workingHours,
                            'remarks' => $punch['Remark'] ?? $attendance->remarks,
                            'platform' => 'attendance machine',
                        ]);
                    }
                }

                $count++;
            }

            $this->info("Successfully synced {$count} attendance records.");
            return 0;

        } catch (\Exception $e) {
            $this->error("An error occurred during sync: " . $e->getMessage());
            Log::error("Attendance Sync Error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return 1;
        }
    }
}
