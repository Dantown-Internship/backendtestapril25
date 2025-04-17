<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyExpenseReports;
use Illuminate\Console\Command;

class SendWeeklyReports extends Command
{
    protected $signature = 'reports:weekly';
    protected $description = 'Send weekly expense reports to admins';

    public function handle()
    {
        SendWeeklyExpenseReports::dispatch();
        $this->info('Weekly expense reports job dispatched!');
    }
}
