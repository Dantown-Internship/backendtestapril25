<?php

namespace App\Console\Commands;

use App\Jobs\DispatchCompanyExpenseReportJobs;
use App\Jobs\SendCompanyWeeklyReportJob;
use App\Models\Company;
use Illuminate\Console\Command;

class SendCompanyWeeklyExpenseReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Weekly Company Expense Report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // SendCompanyWeeklyReportJob::dispatchSync(Company::first());
        DispatchCompanyExpenseReportJobs::dispatch();
    }
}
