<?php

namespace App\Jobs;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\WeeklyExpenseReportNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendExpenseReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $userIds)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();


        // Fetch expenses with eager loading in a single query
        $expenses = Expense::whereIn('user_id', $this->userIds)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->with('user:id,name,email')
            ->get();

        // Calculate the totals only once
//        $totalAmount = $expenses->sum('amount');

        $categoryTotals = $expenses->groupBy('category')
            ->map(fn($items) => $items->sum('amount'))->toArray();

        User::whereIn('id', $this->userIds)
            ->with('company:id,name')
            ->chunk(100, function ($users) use ($categoryTotals, $expenses, $lastWeekStart, $lastWeekEnd) {
                foreach ($users as $user) {
                    // Filter expenses specific to this user
                    $userExpenses = $expenses->where('user_id', $user->id);
                    $totalAmount = $userExpenses->sum('amount');

                    // Only send notification if user has expenses
                    if ($userExpenses->isNotEmpty()) {
                        $user->notify(new WeeklyExpenseReportNotification(
                            $userExpenses,
                            $totalAmount,
                            $categoryTotals,
                            $lastWeekStart,
                            $lastWeekEnd
                        ));
                    }
                }
            });
    }
}
