<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReportMail;
use App\Notifications\WeeklyExpenseReportNotification;

class WeeklyExpenseReport extends Command
{

    protected $signature = 'app:weekly-expense';

    protected $description = 'Send weekly expense report to all admins';


    public function handle()
    {
        $admins = User::where('role', 'Admin')->with('company')->get();

        foreach ($admins as $admin) {
            $expenses = Expense::with('user')
                ->where('company_id', $admin->company_id)
                ->where('created_at', '>=', now()->subWeek())
                ->get();

            if ($expenses->isEmpty()) {
                continue;
            }

            Mail::to($admin->email)->send(new WeeklyExpenseReportMail($admin, $expenses));
        }

        $this->info('Weekly expense reports sent to all admins.');
    }

}
