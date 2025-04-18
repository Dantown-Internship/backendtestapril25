<?php

namespace App\Jobs;

use App\Models\User; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mails\ExpenseReport;
use Illuminate\Support\Facades\Log;

class SendExpenseReportJob implements ShouldQueue
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
        Log::info('Starting to send weekly expense reports...');
        // Get all Admin users or a subset of users
        $users = User::all();  // Adjust to target specific users, if needed (e.g., admins)

        // Loop through all users and send their specific expense report
        foreach ($users as $user) {
        $userExpenses = $user->expenses;  // Get expenses for this user

            // Check if the user has any expenses, then send the report
        if ($userExpenses->count() > 0) {
                // Send the report via email to this user
        Mail::to($user->email)->send(new \App\Mail\ExpenseReport($userExpenses)); // Pass the user's expenses to the mailable
        Log::info("Expense report sent to: {$user->email}");
            }
            else {
                Log::info("No expenses for: {$user->email}");
            }
        }
        Log::info('Finished sending weekly expense reports.');
    }
}
