<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('app:send-course-start-reminder')
//     ->dailyAt('20:00');

Schedule::command('app:send-course-start-reminder')
    ->everyMinute();

Schedule::command('app:send-course-exam-reminder')
    ->everyMinute();

Schedule::command('app:check-course-attendance')
    ->everyMinute();

Schedule::command('app:check-missing-exam-attendance')
    ->everyMinute();
