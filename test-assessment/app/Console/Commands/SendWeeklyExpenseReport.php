<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyExpenseReport as JobsSendWeeklyExpenseReport;
use App\Mail\ExpenseReportMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:process';

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
        dispatch(new JobsSendWeeklyExpenseReport());
    }
}
