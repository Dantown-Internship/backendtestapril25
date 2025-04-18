<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Expense;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ExpenseReportMail;

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
        $companies = Company::with(['users' => function ($q) {
            $q->where('role', 'Admin');
        }])->get();

        foreach ($companies as $company) {
            $expenses = $company->expenses()
                ->with('user')
                ->whereBetween('created_at', [now()->subWeek(), now()])
                ->get();

            if ($expenses->isEmpty()) {
                continue;
            }

            foreach ($company->users as $admin) {
                Mail::to($admin->email)->send(
                    new ExpenseReportMail($expenses, $company->name)
                );
            }
        }
    }
}
