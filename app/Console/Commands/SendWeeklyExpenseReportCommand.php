<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyReport;
use Illuminate\Console\Command;

class SendWeeklyExpenseReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-expense-reports-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly expense reports to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $job = new SendWeeklyReport();
        $job->handle();

        $this->info("Weekly expense reports sent successfully!");
    }
}
