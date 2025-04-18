<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:send-weekly-expense-report')->everySecond();
    }

    protected function commands(): void
    {
        // This will auto-discover commands from app/Console/Commands
        $this->load(__DIR__ . '/Commands');
    }
}
