<?php

namespace App\Console\Commands;

use App\Jobs\SendCurrentWeekExpenseReport;
use Illuminate\Console\Command;

class SendCurrentWeekReport extends Command
{
    protected $signature = 'expenses:current-week-report';
    protected $description = 'Send expense report for current week immediately';

    public function handle()
    {
        $this->info('Dispatching current week expense report...');
        SendCurrentWeekExpenseReport::dispatch();
        $this->info('Current week expense report job dispatched successfully!');
    }
} 