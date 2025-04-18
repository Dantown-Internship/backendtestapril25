<?php

namespace App\Jobs;

use App\Mail\WeeklyExpenseReport;
use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWeeklyExpenseReports implements ShouldQueue
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
        Company::chunk(100, function ($companies) {
            foreach ($companies as $company) {
                $admins = User::where('company_id', $company->id)
                    ->where('role', 'Admin')
                    ->get();

                $expenses = $company->expenses()
                    ->whereBetween('created_at', [now()->subWeek(), now()])
                    ->get();

                foreach ($admins as $admin) {
                    Mail::to($admin)->queue(new WeeklyExpenseReport($expenses, $company));
                }
            }
        });
    }
}
