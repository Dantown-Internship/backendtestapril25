<?php

namespace App\Jobs;

use App\Models\Company;
use App\Mail\WeeklyExpenseReport;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpenseReport implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Process companies in chunks of 100 to optimize memory
        Company::with(['users' => function ($query) {
            $query->where('role', 'Admin'); // Only fetch admin users
        }])->chunk(100, function ($companies) {
            foreach ($companies as $company) {
                $this->processCompanyReport($company);
            }
        });
    }

    protected function processCompanyReport(Company $company): void
    {
        $reportData = [
            'company' => $company->name,
            'expenses' => $this->getExpensesData($company),
            'timeframe' => $this->getTimeframe(),
            'total' => $this->calculateTotal($company) 
        ];

        // Send report to each admin in the company
        foreach ($company->users as $admin) {
            $this->sendEmail($admin, $reportData);
        }
    }

    protected function getExpensesData(Company $company): array
    {
        // Group expenses by category for the past week
        return $company->expenses()
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->get()
            ->groupBy('category')
            ->map(function ($expenses) {
                return [
                    'total' => $expenses->sum('amount'),
                    'items' => $expenses
                ];
            })->toArray();
    }

    protected function sendEmail(User $admin, array $reportData): void
    {
        try {
            Mail::to($admin->email)
                ->send(new WeeklyExpenseReport($reportData));
            
            Log::info("Sent weekly report to {$admin->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send to {$admin->email}: {$e->getMessage()}");
        }
    }

    protected function getTimeframe(): string
    {
        // Format as "Sep 1 - Sep 8, 2023"
        return now()->subWeek()->format('M j') . ' - ' . now()->format('M j, Y');
    }

    protected function calculateTotal(Company $company): float
    {
        // Sum all expenses for the company in the past week
        return $company->expenses()
            ->whereBetween('created_at', [now()->subWeek(), now()])
            ->sum('amount');
    }
}