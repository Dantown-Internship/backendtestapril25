<?php

namespace App\Console\Commands;

use App\Facades\ExpenseReportPdfGenerator;
use App\Models\User;
use App\Notifications\ExpenseReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendExpenseWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-expense-weekly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly expense report to admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $admins = User::whereAdmin()->get();
        $startDate = now()->subWeek()->startOfWeek();
        $endDate = now()->subWeek()->endOfWeek();
        ExpenseReportPdfGenerator::generate($admins, $startDate, $endDate);
        Notification::send($admins, new ExpenseReport($startDate, $endDate));
    }
}
