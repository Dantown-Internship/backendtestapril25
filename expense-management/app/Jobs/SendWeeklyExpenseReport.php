<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        Company::chunk(100, function($companies) {
            foreach ($companies as $company) {
                $admins = $company->users()->where('role', 'Admin')->get();
                
                if ($admins->isEmpty()) continue;
                
                $expenses = $company->expenses()
                    ->whereBetween('created_at', [now()->subWeek(), now()])
                    ->get();
                
                $total = $expenses->sum('amount');
                
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new WeeklyExpenseReport($expenses, $total));
                }
            }
        });
    }
}
