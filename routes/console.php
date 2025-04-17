<?php

use App\Jobs\SendExpenseReport;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendExpenseReport)
    ->weeklyOn(1, '08:00')
    ->onOneServer(); // Optional: ensures the job runs on only one server if you have multiple