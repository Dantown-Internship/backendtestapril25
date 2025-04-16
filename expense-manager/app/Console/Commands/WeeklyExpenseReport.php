<?php

namespace App\Console\Commands;

use App\Jobs\WeeklyExpenseReportJob;
use Illuminate\Console\Command;

class WeeklyExpenseReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:expense-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the weekly expense for all companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching expense report job...');
        dispatch(new WeeklyExpenseReportJob());
        $this->info('Job dispatched successfully!');

        return Command::SUCCESS;
    }
}
