<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\{Expense, User};
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all Admins
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            $expenses = Expense::where('company_id', $admin->company_id)
                ->where('created_at', '>=', now()->subWeek())
                ->get();

            // Build report (simplified for now)
            $summary = [
                'total' => $expenses->sum('amount'),
                'count' => $expenses->count(),
                'details' => $expenses->toArray(),
            ];

            // Send the mail
            Mail::to($admin->email)->queue(new \App\Mail\WeeklyExpenseReport($admin, $summary));
        }
    }
}
