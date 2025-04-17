<?php

namespace App\Jobs;

use App\Enums\UserRole;
use App\Mail\WeeklyExpenseReport;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
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
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Get admin users for each company
            $admins = User::where('company_id', $company->id)
                ->where('role', UserRole::ADMIN->value)
                ->get();

            // Get weekly expenses for this company
            $weeklyExpenses = Expense::with('user')
                ->where('company_id', $company->id)
                ->whereBetween('created_at', [now()->subWeek(), now()])
                ->get();

            // Only send report if there are expenses
            if ($weeklyExpenses->isEmpty()) {
                continue;
            }

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new WeeklyExpenseReport($admin, $company, $weeklyExpenses));
            }
        }
    }
}
