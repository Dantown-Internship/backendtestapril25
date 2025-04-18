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

class SendCurrentWeekExpenseReport implements ShouldQueue
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
        // Use current week
        $startDate = now()->startOfWeek();
        $endDate = now();

        $expenses = Expense::where('company_id', $company->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Add logging for debugging
        \Log::info("Processing current week report for company: {$company->name}");
        \Log::info("Date range: {$startDate} to {$endDate}");
        \Log::info("Found {$expenses->count()} expenses");

        $statistics = [
            'total_amount' => $expenses->sum('amount'),
            'average_amount' => $expenses->avg('amount') ?? 0,
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

        // Find and notify admins
        $admins = User::where('company_id', $company->id)
            ->where('role', 'Admin')
            ->get();

        \Log::info("Found {$admins->count()} admins for company: {$company->name}");

        foreach ($admins as $admin) {
            \Log::info("Sending current week report to: {$admin->email}");
            $admin->notify(new WeeklyExpenseReport($statistics, $expenses));
        }
    }
} 