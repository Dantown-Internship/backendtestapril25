<?php

namespace App\Console\Commands;

use App\Jobs\GenerateWeeklyExpenseReport;
use Illuminate\Console\Command;

class SendWeeklyExpenseReport extends Command
{
    protected $signature = 'expenses:weekly-report';
    protected $description = 'Generate and send weekly expense reports';

    public function handle()
    {
        $this->info('Dispatching weekly expense report job...');
        GenerateWeeklyExpenseReport::dispatch();
        $this->info('Weekly expense report job dispatched successfully!');
    }
} 