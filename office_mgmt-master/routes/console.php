<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
Use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:mark-missing')->dailyAt('23:47');
Schedule::command('attendance:sync-api')->everyFiveMinutes();