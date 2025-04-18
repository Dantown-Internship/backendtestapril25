<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyReportEmail; // We'll create this Mailable

class WeeklyExpenseReport implements ShouldQueue
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
        $companies = Company::all();

        foreach ($companies as $company) {
            $lastWeekStart = now()->subWeek()->startOfWeek();
            $lastWeekEnd = now()->subWeek()->endOfWeek();

            $expenses = Expense::where('company_id', $company->id)
                ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->get();

            $admins = User::where('company_id', $company->id)
                ->where('role', \App\Enums\Roles::ADMIN->value)
                ->get();

            if ($admins->isNotEmpty() && $expenses->isNotEmpty()) {
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new WeeklyReportEmail($company, $expenses));
                }
            }
        }
    }
}