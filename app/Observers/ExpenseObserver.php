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

    public function creating(Expense $expense): void
    {
        // $expense->company_id = auth()->user()->company_id;
        // or with a `team` relationship defined:
        $expense->company()->associate(auth()->user()->company);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        //
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'company_id' => $expense->company_id,
            'changes' => json_encode($expense->getChanges()),
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        //
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'company_id' => $expense->company_id,
            'changes' => json_encode($expense->getChanges()),

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
