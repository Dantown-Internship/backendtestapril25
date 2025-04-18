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
    protected $signature = 'app:send-weekly-expense-report';

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
        $this->info('✅ Job dispatched!');
    }
}
