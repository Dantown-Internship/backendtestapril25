<?php

namespace App\Console;

use App\Jobs\SendWeeklyExpenseReport;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Send weekly expense reports every Monday at 8:00 AM
        $schedule->job(new SendWeeklyExpenseReport)->weekly()->mondays()->at('8:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}