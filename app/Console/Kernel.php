<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendWeeklyExpenseReport;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the job to run every week (Sunday at midnight)
        $schedule->job(new SendWeeklyExpenseReport)->weeklyOn(0, '00:00');
     

    }

    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
