<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReportMail;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WeeklyReportMail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle():void
    {
        // Fetch all admins
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {

            // Get this admin's company expenses from the past 7 days
            $expenses = Expense::where('company_id', $admin->company_id)
                ->where('created_at', ">=", [now()->subWeek()])
                ->get();

            // Example: Log the data instead of sending an email (can be replaced with Mail::to()->send())
            Log::info("Sending weekly expense report to {$admin->email}", [
                'expense_count' => $expenses->count(),
            ]);

            // Send report via email
            Mail::to($admin->email)->send(new WeeklyExpenseReportMail($admin, $expenses));
        }
    }
}
