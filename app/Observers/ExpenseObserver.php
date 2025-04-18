<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Expense;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $changes = $expense->getChanges();
        $original = $expense->getOriginal();

        $filteredChanges = [];
        foreach ($changes as $key => $value) {
            if ($key !== 'updated_at') {
                $filteredChanges[$key] = [
                    'old' => $original[$key],
                    'new' => $value,
                ];
            }
        }

        if (!empty($filteredChanges)) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => $expense->company_id,
                'action' => 'update',
                'model_type' => get_class($expense),
                'model_id' => $expense->id,
                'changes' => $filteredChanges,
            ]);
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => $expense->company_id,
            'action' => 'delete',
            'model_type' => get_class($expense),
            'model_id' => $expense->id,
            'changes' => $expense->toArray(),
        ]);
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }
}
