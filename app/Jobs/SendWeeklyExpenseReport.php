<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Mail\WeeklyExpenseReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            // Get expenses for the past week
            $weeklyExpenses = Expense::where('company_id', $company->id)
                ->whereBetween('created_at', [now()->subWeek(), now()])
                ->with('user:id,name')
                ->get();

            // Get total amount
            $totalAmount = $weeklyExpenses->sum('amount');

            // Get all admins for the company
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new WeeklyExpenseReport(
                    $admin,
                    $company,
                    $weeklyExpenses,
                    $totalAmount
                ));
            }
        }
    }
}