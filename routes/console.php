<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\{Artisan, Schedule};

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('expense:send-weekly-report')
    ->weeklyOn(1, '08:00')
    ->appendOutputTo(storage_path('logs/weekly-report.log'));
