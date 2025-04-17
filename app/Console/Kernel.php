<?php

namespace App\Console;

use App\Jobs\SendWeeklyExpenseReportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * send report every monday 8:00 am 
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new SendWeeklyExpenseReportJob)
        ->weeklyOn(1, '08:00')
        ->withoutOverlapping()
        ->onOneServer()
        ->sendOutputTo(storage_path('logs/weekly_expense_report.log'));

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
