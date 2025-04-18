<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Expense;

class ExpenseObserver
{
    public function updating(Expense $expense) {
        // Get old data before update
        $original = $expense->getOriginal();

        // After update, we compare the changes
        $changes = $expense->getDirty();

        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $expense->company_id,
            'action'     => 'updated',
            'changes'    => json_encode([
                'old' => array_intersect_key($original, $changes),
                'new' => $changes
            ]),
        ]);
    }

    public function deleting(Expense $expense) {

        AuditLog::create([
            'user_id'    => auth()->id(),
            'company_id' => $expense->company_id,
            'action'     => 'deleted',
            'changes'    => json_encode($expense->toArray()),
        ]);
    }
}
