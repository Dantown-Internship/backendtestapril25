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
    protected function schedule(Schedule $schedule)
    {
        // Schedule the weekly expense report job for every Monday at 8:00 AM
        $schedule->job(new SendWeeklyExpenseReport)->weeklyOn(1, '8:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}