<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('schedule:weekly-report', function () {
    // Dispatch the job
    dispatch(new \App\Jobs\SendWeeklyExpenseReport);
})->describe('Dispatch the weekly expense report job');

// Schedule the command
app(Schedule::class)->command('schedule:weekly-report')->weekly();
