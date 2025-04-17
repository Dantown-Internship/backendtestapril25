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

class GenerateExpenseReport implements ShouldQueue
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
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Get all admin users for the company
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            if ($admins->isEmpty()) {
                continue;
            }

            // Get expenses for the last week
            $expenses = $company->expenses()
                ->whereBetween('created_at', [date('Y-m-d H:i:s', strtotime('-7 days')), date('Y-m-d H:i:s')])
                ->with('user:id,name')
                ->get();

            // Calculate summary
            $summary = [
                'total_expenses' => $expenses->sum('amount'),
                'expense_count' => $expenses->count(),
                'average_expense' => $expenses->avg('amount'),
                'categories' => $expenses->groupBy('category')
                    ->map(function ($categoryExpenses) {
                        return [
                            'total' => $categoryExpenses->sum('amount'),
                            'count' => $categoryExpenses->count(),
                        ];
                    }),
            ];

            // Send email to each admin
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new WeeklyExpenseReport($summary, $company, $admin));
            }
        }
    }
}
