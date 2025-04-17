<?php

namespace App\Actions\Expense;

use App\Models\Expense;

class GetExpenseByIdAction
{
    public function __construct(
        private Expense $expense
    )
    {
        
    }
    public function execute(string $expenseId, array $relationships = [])
    {
        return $this->expense->with(
            $relationships
        )->where([
            'id' => $expenseId
        ])->first();
    }
}