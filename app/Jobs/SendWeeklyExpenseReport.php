<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WeeklyExpenseReport;

class SendWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
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

            // Get last week's expenses
            $expenses = $company->expenses()
                ->whereBetween('created_at', [
                    now()->subWeek()->startOfWeek(),
                    now()->subWeek()->endOfWeek()
                ])
                ->with(['user:id,name'])
                ->get();

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)
                    ->send(new WeeklyExpenseReport($company, $expenses));
            }
        }
    }
} 