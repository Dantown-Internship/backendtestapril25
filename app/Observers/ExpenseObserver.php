<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\Contracts\AuditLogServiceInterface;

class ExpenseObserver
{

    private $auditLogService;

    public function __construct(AuditLogServiceInterface $auditLogService)
    {
        $this->auditLogService = $auditLogService;
    }

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
        $expense->old = array_intersect_key($expense->getOriginal(), $expense->getChanges());
        $this->auditLogService->log('updated', $expense->old, $expense->getChanges());
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $expense->old = array_intersect_key($expense->getOriginal(), $expense->getChanges());
        $this->auditLogService->log('deleted', $expense->old, []);
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
