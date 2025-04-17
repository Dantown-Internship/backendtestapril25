<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseObserver
{

    public function clearExpenseCache(Expense $expense)
    {
        $companyId = $expense->company_id;

        // Use a pattern to forget all related cache entries.
        $pattern = "expenses:company:{$companyId}:*";

        // Laravel doesn't support cache pattern deletion directly,
        // but you can do it using Redis facade directly:
        foreach (Cache::getRedis()->keys($pattern) as $key) {
            Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
    }

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->clearExpenseCache($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $this->clearExpenseCache($expense);

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => $expense->company_id,
            'action' => 'updated',
            'changes' => json_encode([
                'old' => $expense->getOriginal(),
                'new' => $expense->getChanges(),
            ]),
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $this->clearExpenseCache($expense);

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => $expense->company_id,
            'action' => 'deleted',
            'changes' => json_encode([
                'old' => $expense->toArray(),
                'new' => null,
            ]),
        ]);

    }
}
