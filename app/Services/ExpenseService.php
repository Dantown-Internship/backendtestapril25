<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;
use App\Services\Interfaces\ExpenseServiceInterface;

class ExpenseService implements ExpenseServiceInterface
{
    public function createExpense(array $data): Expense
    {
        $expense = Expense::create($data);
        Cache::forget("expenses:company:{$data['company_id']}");

        return $expense;
    }

    public function updateExpense(Expense $expense, array $data): bool
    {
        $oldData = $expense->toArray();
        $updated = $expense->update($data);

        if ($updated) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'action' => 'update',
                'changes' => [
                    'old' => $oldData,
                    'new' => $expense->fresh()->toArray(),
                ],
            ]);

            Cache::forget("expenses:company:{$expense->company_id}");
        }

        return $updated;
    }

    public function deleteExpense(Expense $expense): bool
    {
        $oldData = $expense->toArray();
        $deleted = $expense->delete();

        if ($deleted) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'action' => 'delete',
                'changes' => [
                    'old' => $oldData,
                    'new' => null,
                ],
            ]);

            Cache::forget("expenses:company:{$expense->company_id}");
        }

        return $deleted;
    }
}
