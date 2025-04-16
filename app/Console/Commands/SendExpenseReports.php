<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendWeeklyExpenseReport;

class SendExpenseReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-expense-reports';

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
        SendWeeklyExpenseReport::dispatch();
        $this->info('Weekly expense reports sent successfully!');
    }
}
