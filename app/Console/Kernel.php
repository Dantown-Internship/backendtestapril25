<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendWeeklyExpenseReports;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected function schedule(Schedule $schedule)
{
    $schedule->command('reports:weekly')
             ->weekly() // Runs weekly on Sundays at 00:00
             ->mondays()
             ->at('08:00')
             ->withoutOverlapping();
}

    protected $routeMiddleware = [
        'company' => \App\Http\Middleware\EnsureCompanyAccess::class,
        'role' => \App\Http\Middleware\CheckRole::class,
    ];

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}