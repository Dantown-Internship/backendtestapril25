<?php

namespace App\Jobs;

use App\Models\Company;
use App\Mail\WeeklyExpenseReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $companies = Company::with(['users' => function ($query) {
            $query->where('role', 'Admin');
        }])->get();

        foreach ($companies as $company) {
            $expenses = $company->expenses()
                ->whereBetween('created_at', [now()->subWeek(), now()])
                ->get();

            $total = $expenses->sum('amount');

            foreach ($company->users->where('role', 'Admin') as $admin) {
                Mail::to($admin->email)->send(new WeeklyExpenseReport($expenses, $total));
            }
        }
    }
}
