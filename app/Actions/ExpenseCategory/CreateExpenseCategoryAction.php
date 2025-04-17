<?php

namespace App\Actions\ExpenseCategory;

use App\Models\ExpenseCategory;

class CreateExpenseCategoryAction
{
    public function __construct(
        private ExpenseCategory $expenseCategory
    )
    {}

    public function execute(array $createExpenseCategoryRecordOptions)
    {
        return $this->expenseCategory->create(
            $createExpenseCategoryRecordOptions
        );
    }
}