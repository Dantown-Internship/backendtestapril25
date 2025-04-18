<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\Audit_Log;
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
    public function updating(Expense $expense): void
    {
        $original = $expense->getOriginal();
        $changes = $expense->getDirty();

        Audit_Log::create([
            'user_id' => Auth::id(),
            'company_id' => $expense->company_id,
            'action' => 'update',
            'changes' => [
                'old' => array_intersect_key($original, $changes),
                'new' => $changes,
            ],
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleting(Expense $expense): void
    {
        Audit_Log::create([
            'user_id' => Auth::id(),
            'company_id' => $expense->company_id,
            'action' => 'delete',
            'changes' => [
                'old' => $expense->toArray(),
                'new' => null,
            ],
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
