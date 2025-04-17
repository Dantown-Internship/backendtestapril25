<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReport;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
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
    public function handle()
    {
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Get all expenses for this company in the last week
            $startDate = Carbon::now()->subWeek();
            $endDate = Carbon::now();

            $expenses = Expense::with('user')
                ->where('company_id', $company->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Calculate total expenses
            $totalAmount = $expenses->sum('amount');

            // Get all admins for this company
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)->queue(new WeeklyExpenseReport(
                    $admin,
                    $company,
                    $expenses,
                    $totalAmount,
                    $startDate,
                    $endDate
                ));
            }
        }
    }
}
