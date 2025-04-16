<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use App\Traits\AuditLogTrait;
use Illuminate\Support\Facades\Gate;

class DeleteExpensesAction
{
    use AuditLogTrait;

    public function handle($id)
    {
        $expense = Expense::findOrFail($id);

        Gate::authorize('delete', $expense);
        // get old data for logging
        $oldData = $expense->toArray();
        // create audit log
        $this->storeAudit('delete', $oldData);
        $expense->delete();
    }
}
