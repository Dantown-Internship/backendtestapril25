<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseObserver
{
    public function created(Expense $expense)
    {
        $this->logAudit($expense, 'created');
    }

    public function updated(Expense $expense)
    {
        $this->logAudit($expense, 'updated');
    }

    public function deleted(Expense $expense)
    {
        $this->logAudit($expense, 'deleted');
    }

    protected function logAudit(Expense $expense, string $action)
    {
        $userId = Auth::check() ? Auth::id() : null;

        AuditLog::create([
            'user_id' => $userId,
            'company_id' => $expense->company_id,
            'action' => $action,
            'model_type' => get_class($expense),
            'model_id' => $expense->id,
            'changes' => $action === 'updated'
                ? $this->getChanges($expense)
                : null,
        ]);
    }

    protected function getChanges(Expense $expense)
    {
        $changes = [];
        foreach ($expense->getChanges() as $attribute => $newValue) {
            if (!in_array($attribute, ['created_at', 'updated_at'])) {
                $changes[$attribute] = [
                    'old' => $expense->getOriginal($attribute),
                    'new' => $newValue,
                ];
            }
        }
        return $changes;
    }
}