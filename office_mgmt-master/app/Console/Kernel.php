<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\MarkAttendanceForAllUsers::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Define your scheduled tasks here
    }

    public function commands()
    {
        $this->load(__DIR__.'/Commands');
        
    }
}