<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReport;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function handle() {
        // Grab all admins
        $admins = User::where('role', 'Admin')->get();

        // For each admin, pull their company's last-week expenses
        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                ->whereBetween('created_at', [
                    now()->subWeek(), now()
                ])->get();

            // send the report
            Mail::to($admin->email)
                ->send(new WeeklyExpenseReport($expenses));
        }
    }
}
