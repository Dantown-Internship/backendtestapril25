<?php

namespace App\Actions\Expenses;

use App\Models\Expense;
use App\Traits\AuditLogTrait;
use Illuminate\Support\Facades\Gate;

class UpdateExpensesAction
{
    use AuditLogTrait;
    public function handle($id, $validated)
    {
        $expense = Expense::findOrFail($id);

        Gate::authorize('update', $expense);
        // store old data for audit log
        $oldData = $expense->toArray();

        $expense->update($validated);
        // create audit log
        $this->storeAudit('update', $oldData, $expense->toArray());
        return $expense;
    }
}
