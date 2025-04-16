<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use App\Traits\AuditLogTrait;

class DeleteExpensesAction
{
    use AuditLogTrait;

    public function handle($id)
    {
        $expense = Expense::findOrFail($id);
        // get old data for logging
        $oldData = $expense->toArray();
        // create audit log
        $this->storeAudit('delete', $oldData);
        $expense->delete();
    }
}
