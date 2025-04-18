<?php

namespace App\Console\Commands;

use App\Jobs\SendExpenseReportJob;
use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;

class SendWeeklyExpenseReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-expense-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to send weekly expense reports to all company admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::where('role', 'Admin')
            ->select('users.*')
            ->chunk(100, function ($admins) {
                $ids = $admins->pluck('id')->toArray();
                SendExpenseReportJob::dispatch($ids);
            });

    }
}
