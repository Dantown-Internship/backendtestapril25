<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyExpenseReport;
use Illuminate\Console\Command;

class SendWeeklyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly expense reports to company admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching weekly expense report job...');
        SendWeeklyExpenseReport::dispatch();
        $this->info('Weekly expense report job dispatched!'); 
        return 0;
    }
}
