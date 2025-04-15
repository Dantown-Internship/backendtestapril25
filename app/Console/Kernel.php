<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendWeeklyExpenseReport;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the job to run every Monday at 8am
        $schedule->job(new SendWeeklyExpenseReport)
                ->weekly(1, '08:00') // Adjusted for UTC+1
                ->onSuccess(function () {
                    logger('Weekly Expense Report Job completed successfully.');
                })
                ->onFailure(function () {
                    logger('Weekly Expense Report Job failed.');
                });
    }
}