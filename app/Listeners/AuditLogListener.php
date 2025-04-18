<?php

namespace App\Listeners;

use App\Events\ExpenseDeleted;
use App\Events\ExpenseUpdated;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AuditLogListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the ExpenseUpdated event.
     */
    public function handleExpenseUpdated(ExpenseUpdated $event): void
    {
        $changes = [];
        foreach ($event->changedAttributes as $key => $newValue) {
            $changes[$key] = [
                'old' => $event->originalAttributes[$key] ?? null,
                'new' => $newValue,
            ];
        }

        echo "Changes detected: Details below...\n";
        var_dump($event->originalAttributes);

        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => $event->expense->company_id,
                'action' => 'expense_updated',
                'changes' => $changes,
            ]);
        } else {
            echo "No changes detected, not logging audit entry.\n";
        }
    }

    /**
     * Handle the ExpenseDeleted event.
     */
    public function handleExpenseDeleted(ExpenseDeleted $event): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => $event->expense->company_id,
            'action' => 'expense_deleted',
            'changes' => null, // No specific changes to log for deletion
        ]);
    }
}