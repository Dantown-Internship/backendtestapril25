<?php

namespace App\Actions\Expense;

use App\Models\Expense;

class DeleteExpenseAction
{
    public function __construct(
        private Expense $expense
    )
    {
        
    }
    public function execute(string $expenseId)
    {
        return $this->expense->where([
            'id' => $expenseId
        ])->delete();
    }
}