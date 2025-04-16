<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use App\Traits\AuditLogTrait;

class UpdateExpensesAction
{
    use AuditLogTrait;
    public function handle($id, $validated)
    {
        $expense = Expense::findOrFail($id);
        // store old data for audit log
        $oldData = $expense->toArray();

        $expense->update($validated);
        // create audit log
        $this->storeAudit('update', $oldData, $expense->toArray());
        return $expense;
    }
}
