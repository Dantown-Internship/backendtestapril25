<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Find admin users
            $admins = User::where('company_id', $company->id)
                          ->where('role', 'Admin')
                          ->get();

            // Get weekly expenses
            $expenses = Expense::where('company_id', $company->id)
                              ->whereBetween('created_at', [
                                  now()->subWeek()->startOfWeek(),
                                  now()->subWeek()->endOfWeek()
                              ])->get();

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new WeeklyExpenseReport($company, $expenses));
            }
        }
    }
}
