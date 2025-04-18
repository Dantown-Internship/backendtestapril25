<?php
// app/Services/ExpenseService.php
namespace App\Services;

use App\Models\Tenant\Expense;
use App\Models\Tenant\AuditLog;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{
    public function createExpense(array $data, $user)
    {
        $expense = Expense::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'amount' => $data['amount'],
            'category' => $data['category'],
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'expense_created',
            'changes' => ['new' => $expense->toArray()],
        ]);

        return $expense;
    }

    public function updateExpense(Expense $expense, array $data, $user)
    {
        $oldData = $expense->toArray();
        $expense->update($data);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'expense_updated',
            'changes' => ['old' => $oldData, 'new' => $expense->toArray()],
        ]);

        return $expense;
    }

    public function deleteExpense(Expense $expense, $user)
    {
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'expense_deleted',
            'changes' => ['old' => $expense->toArray()],
        ]);

        $expense->delete();
    }
}