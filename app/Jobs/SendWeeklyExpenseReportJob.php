<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\WeeklyExpenseReportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SendWeeklyExpenseReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Get last week's date range
            $startDate = Carbon::now()->subWeek()->startOfWeek();
            $endDate = Carbon::now()->subWeek()->endOfWeek();

            // Get expenses for the company in the last week
            $expenses = Expense::where('company_id', $company->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Get expense stats
            $totalAmount = $expenses->sum('amount');
            $expenseCount = $expenses->count();
            $categoryStats = $expenses->groupBy('category')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'total' => $items->sum('amount'),
                    ];
                });

            // Find top expenses
            $topExpenses = $expenses->sortByDesc('amount')->take(5);

            // Data for report
            $reportData = [
                'company' => $company,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'totalAmount' => $totalAmount,
                'expenseCount' => $expenseCount,
                'categoryStats' => $categoryStats,
                'topExpenses' => $topExpenses,
            ];

            // Send report to all company admins
            $admins = User::where('company_id', $company->id)
                ->where('role', 'Admin')
                ->get();

            foreach ($admins as $admin) {
                $admin->notify(new WeeklyExpenseReportNotification($reportData));
            }
        }
    }
}
