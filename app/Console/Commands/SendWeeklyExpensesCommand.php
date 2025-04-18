<?php

namespace App\Console\Commands;

use App\Jobs\SendWeeklyExpenseReport;
use Illuminate\Console\Command;

class SendWeeklyExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dantown:send-weekly-expenses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly expenses to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendWeeklyExpenseReport::dispatch();
    }
}
