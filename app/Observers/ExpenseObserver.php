<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $changes = $expense->getChanges();
        unset($changes['updated_at']);
        
        if (!empty($changes)) {
            $originalValues = [];
            
            foreach (array_keys($changes) as $key) {
                $originalValues[$key] = $expense->getOriginal($key);
            }
            
            AuditLog::create([
                'user_id' => Auth::id(),
                'company_id' => $expense->company_id,
                'action' => 'updated',
                'changes' => [
                    'old' => $originalValues,
                    'new' => $changes,
                ],
            ]);
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => $expense->company_id,
            'action' => 'deleted',
            'changes' => $expense->getAttributes(),
        ]);
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        $this->deleted($expense);
    }
}
