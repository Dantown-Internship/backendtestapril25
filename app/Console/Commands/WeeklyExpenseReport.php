<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\WeeklyExpenseReportJob;

class WeeklyExpenseReport extends Command
{
    protected $signature = 'weekly-expense-report:generate';
    protected $description = 'Generate a weekly expense report';

    public function handle()
    {
        
        $this->info('Generating weekly expense report...');

        WeeklyExpenseReportJob::dispatch();

        $this->info('Weekly expense report job has been dispatched.');

        return 0;
    }
}