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
        //
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $original = $expense->getOriginal(); // old values
        $changes = $expense->getChanges();   // new values

        AuditLog::create([
            'user_id'    => Auth::id(),
            'company_id' => $expense->company_id,
            'action'     => 'update',
            'changes'    => json_encode([
                'old' => $original,
                'new' => $changes,
            ], JSON_PRETTY_PRINT),
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */

     public function deleted(Expense $expense)
     {
         AuditLog::create([
             'user_id'    => Auth::id(),
             'company_id' => $expense->company_id,
             'action'     => 'delete',
             'changes'    => json_encode([
                 'old' => $expense->toArray(),
                 'new' => null,
             ], JSON_PRETTY_PRINT),
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
