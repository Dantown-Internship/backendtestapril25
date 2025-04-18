<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\WeeklyExpenseReportJob;

class DispatchWeeklyReportJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-weekly-report-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually dispatch the weekly expense report job for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching the WeeklyExpenseReportJob...');
        
        WeeklyExpenseReportJob::dispatch();
        
        $this->info('Job dispatched successfully!');
        $this->comment('Run "php artisan queue:work" to process the job.');
    }
} 