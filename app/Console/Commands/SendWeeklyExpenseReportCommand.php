<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyExpenseReport;
use Illuminate\Console\Command;

class SendWeeklyExpenseReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:send-weekly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually triggers the weekly expense report job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching Weekly Expense Report job...');

        // Create and dispatch the job immediately
        (new SendWeeklyExpenseReport())->handle();

        $this->info('Weekly Expense Report job completed successfully!');
    }
}
