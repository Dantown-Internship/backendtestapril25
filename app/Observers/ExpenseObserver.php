<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\AuditLogService;

class ExpenseObserver
{
    public function __construct(private readonly AuditLogService $auditLogService)
    {
    }

    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        //
    }

     /**
     * Handle the Expense "updating" event.
     */
    public function updating(Expense $expense): void
    {
        $action = 'update_expense_'.$expense->id;
        $newChanges = $expense->getDirty();
        $oldChanges = array_intersect_key($expense->getOriginal(), $newChanges);
        $changes = [
            'old' => $oldChanges,
            'new' => $newChanges
        ];
        logger()->info('Expense changes', $newChanges);
        $this->auditLogService->log($action, $changes);
    }
    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $action = 'delete_expense_'.$expense->id;
        $this->auditLogService->log($action);
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
