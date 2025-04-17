<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReportMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all Admins
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            // Get expenses for the Admin's company created in the last 7 days
            $expenses = Expense::where('company_id', $admin->company_id)
                ->whereDate('created_at', '>=', now()->subWeek())
                ->get();

            // Only send email if there are expenses
            if ($expenses->isNotEmpty()) {
                Mail::to($admin->email)->queue(new WeeklyExpenseReportMail($expenses));
            }
        }
    }
}
