<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\AuditLogService;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function created(Expense $expense): void
    {
        app(AuditLogService::class)->log(
            'created',
            $expense,
            [],
            $expense->toArray()
        );
    }

    /**
     * Handle the Expense "updated" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function updated(Expense $expense): void
    {
        app(AuditLogService::class)->log(
            'updated',
            $expense,
            $expense->getOriginal(),
            $expense->getChanges()
        );
    }

    /**
     * Handle the Expense "deleted" event.
     *
     * @param Expense $expense
     * @return void
     */
    public function deleted(Expense $expense): void
    {
        app(AuditLogService::class)->log(
            'deleted',
            $expense,
            $expense->toArray(),
            []
        );
    }
}
