<?php

namespace App\Observers;

use App\Models\Expense;
use Illuminate\Support\Facades\Cache;

class ExpenseObserver
{
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
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $this->clearExpenseCache($expense);
    }
    
    protected function clearExpenseCache(Expense $expense)
    {
        Cache::tags("expenses:{$expense->company_id}")->flush();
    }
}
