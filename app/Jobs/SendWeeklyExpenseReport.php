<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        foreach(Company::with('users', 'users.expenses')->get() as $company)
        {
            $admins = $company->users()
                ->where('role', 'admin')
                ->get();
            $totalExpenseAmount = $company->expenses()
                ->whereBetween('created_at', now()->subWeek(), now())
                ->sum('amount');

            // Send email to each admin
            foreach($admins as $admin)
            {
                Mail::raw("Your weekly expense report is ready. Total expenses: ₦{$totalExpenseAmount}", function ($message) use ($admin) {
                    $message->to($admin->email)
                            ->subject('Weekly Expense Report');
                });
            }
        }
    }
}
