<?php

use App\Console\Commands\SendWeeklyExpenseReportCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendWeeklyExpenseReportCommand::class)
    ->weekly()
    ->mondays()
    ->at('8:00')
    ->runInBackground()
    ->withoutOverlapping()
    ->onFailure(function () {
        Log::error('Failed to send weekly expense reports');
    });
