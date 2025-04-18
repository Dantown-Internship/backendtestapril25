<?php

namespace App\Jobs;

use App\Enums\ExpenseCategory;
use App\Enums\Role;
use App\Helpers\CacheKey;
use App\Models\Company;
use App\Models\Expense;
use App\Notifications\WeeklyExpenseReportNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendCompanyWeeklyReportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Company $company
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins = cache()->remember(
            CacheKey::companyAdmins($this->company->uuid),
            now()->addDays(30),
            function () {
                return $this->company->users()
                    ->where('role', Role::Admin)
                    ->get();
            }
        );

        $startDate = now()->subWeek();
        $endDate = now();
        $expenseQuery = Expense::query()
            ->where('company_id', $this->company->id)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate);

        $totalExpense = $expenseQuery->clone()->sum('amount');

        $topSpenders = $expenseQuery
            ->clone()
            ->select('user_id', DB::raw('SUM(amount) as total'))
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'user' => $item->user->name,
                'total' => $item->total,
            ]);

        $expenseByCategory = $expenseQuery
            ->clone()
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->map(fn ($item) => [
                'category' => $item->category,
                'total' => $item->total,
            ]);

        $sortedExpenses = collect(ExpenseCategory::cases())
            ->mapWithKeys(function (ExpenseCategory $category) use ($expenseByCategory) {
                $expense = $expenseByCategory->where('category', $category)->first();

                return [
                    $category->label() => $expense['total'] ?? 0,
                ];
            })
            ->sortDesc();

        Notification::send($admins, new WeeklyExpenseReportNotification(
            totalExpense: $totalExpense,
            companyName: $this->company->name,
            sortedExpenses: $sortedExpenses->toArray(),
            topSpenders: $topSpenders->toArray(),
            startDate: $startDate,
            endDate: $endDate
        ));

    }
}
