<?php

namespace App\Actions\ExpenseCategory;

use App\Models\ExpenseCategory;

class DeleteExpenseCategoryAction
{
    public function __construct(
        private ExpenseCategory $expenseCategory
    )
    {
        
    }
    public function execute(string $expenseCategoryId)
    {
        return $this->expenseCategory->where([
            'id' => $expenseCategoryId
        ])->delete();
    }
}