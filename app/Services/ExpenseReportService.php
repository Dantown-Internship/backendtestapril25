<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Expense;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class ExpenseReportService
{
    /**
     * Generate a weekly expense report for a company.
     *
     * @param \App\Models\Company $company
     * @return array
     */
    public function generateWeeklyReport(Company $company): array
    {
        $startDate = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $endDate = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $expenses = Expense::where('company_id', $company->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
            ],
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'summary' => [
                'total_expenses' => $expenses->sum('amount'),
                'average_expense' => $expenses->avg('amount'),
                'expense_count' => $expenses->count(),
            ],
            'categories' => $this->getCategorySummary($expenses),
            'top_expenses' => $this->getTopExpenses($expenses),
        ];
    }

    /**
     * Get a summary of expenses by category.
     *
     * @param \Illuminate\Support\Collection $expenses
     * @return array
     */
    private function getCategorySummary(Collection $expenses): array
    {
        return $expenses->groupBy('category')->map(function ($items) {
            return [
                'total' => $items->sum('amount'),
                'count' => $items->count(),
                'average' => $items->avg('amount'),
            ];
        })->toArray();
    }

    /**
     * Get the top 5 largest expenses.
     *
     * @param \Illuminate\Support\Collection $expenses
     * @return array
     */
    private function getTopExpenses(Collection $expenses): array
    {
        return $expenses->sortByDesc('amount')
            ->take(5)
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'title' => $expense->title,
                    'amount' => $expense->amount,
                    'category' => $expense->category,
                    'date' => $expense->created_at->toDateString(),
                ];
            })
            ->values()
            ->toArray();
    }
}
