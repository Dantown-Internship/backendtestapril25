<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\User;
use App\Models\Expense;
use App\Notifications\WeeklyExpenseReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateWeeklyExpenseReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Process for each company
        Company::chunk(100, function ($companies) {
            foreach ($companies as $company) {
                $this->processCompanyReport($company);
            }
        });
    }

    private function processCompanyReport(Company $company)
    {
        // Get last week's expenses
        $startDate = now()->subWeek()->startOfWeek();
        $endDate = now()->subWeek()->endOfWeek();

        $expenses = Expense::where('company_id', $company->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate statistics
        $statistics = [
            'total_amount' => $expenses->sum('amount'),
            'average_amount' => $expenses->avg('amount'),
            'count' => $expenses->count(),
            'by_category' => $expenses->groupBy('category')
                ->map(fn($items) => [
                    'count' => $items->count(),
                    'total' => $items->sum('amount')
                ]),
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ];

        // Send to all company admins
        User::where('company_id', $company->id)
            ->where('role', 'Admin')
            ->each(function ($admin) use ($statistics, $expenses) {
                $admin->notify(new WeeklyExpenseReport($statistics, $expenses));
            });
    }
} 