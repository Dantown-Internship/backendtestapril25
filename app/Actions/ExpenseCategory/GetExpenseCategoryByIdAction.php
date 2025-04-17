<?php

namespace App\Actions\ExpenseCategory;

use App\Models\ExpenseCategory;

class GetExpenseCategoryByIdAction
{
    public function __construct(
        private ExpenseCategory $expenseCategory
    )
    {
        
    }
    public function execute(string $expenseCategoryId, array $relationships = [])
    {
        return $this->expenseCategory->with(
            $relationships
        )->where([
            'id' => $expenseCategoryId
        ])->first();
    }
}