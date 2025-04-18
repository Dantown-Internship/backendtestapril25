<?php

use App\Jobs\WeeklyExpenseReport;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduled task to send weekly expense reports
Schedule::job(new WeeklyExpenseReport())->weekly()->thursdays()->at('14:35'); // Run every Monday at 8:00 AM
// Schedule::job(new WeeklyExpenseReport())->weekly()->mondays()->at('08:00'); // Run every Monday at 8:00 AM
        // You can adjust the frequency and time as needed