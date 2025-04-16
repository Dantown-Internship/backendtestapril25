<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use App\Services\ExpenseReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WeeklyExpenseReportJob implements ShouldQueue
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
    public function handle(ExpenseReportService $expenseReportService): void
    {
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Get all admin users for this company
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            if ($admins->isEmpty()) {
                continue;
            }

            // Generate the report
            $report = $expenseReportService->generateWeeklyReport($company);

            // Send the report to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new \App\Mail\WeeklyExpenseReport($report));
            }
        }
    }
}
