<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Exports\ExpensesExport;
use App\Jobs\ExpenseReportJob;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\ReportNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expense-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('*** initializing expense report ***');
        ExpenseReportJob::dispatch();
        $this->info('Weekly Expenses Done');
    }
}
