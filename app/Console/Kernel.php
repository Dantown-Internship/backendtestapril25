<?php

namespace App\Console;

use App\Jobs\ProcessWeeklyExpenseReportSending;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->job(new ProcessWeeklyExpenseReportSending())->everyMinute(); // To test right away
        $schedule->job(new ProcessWeeklyExpenseReportSending)
            ->weekly()
            ->mondays()
            ->at('8:00')
            ->timezone('Africa/Lagos');
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
