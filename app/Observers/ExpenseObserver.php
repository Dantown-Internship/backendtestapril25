<?php

namespace App\Observers;

use App\Casts\AuditLogChanges;
use App\DataTransferObjects\AuditLogChangesDto;
use App\Enums\AuditLogAction;
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
        $changes = new AuditLogChangesDto(
            old: $expense->getOriginal(),
            new: $expense->getAttributes(),
        );
        AuditLog::create([
            'company_id' => $expense->company_id,
            'user_id' => auth()->id(),
            'action' => AuditLogAction::Update,
            'changes' => $changes,
        ]);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        $changes = new AuditLogChangesDto(
            old: $expense->getAttributes(),
            new: null,
        );
        AuditLog::create([
            'company_id' => $expense->company_id,
            'user_id' => auth()->id(),
            'action' => AuditLogAction::Delete,
            'changes' => $changes,
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
