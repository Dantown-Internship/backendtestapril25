<?php

namespace App\Actions\ExpenseCategory;

use App\Models\ExpenseCategory;

class UpdateExpenseCategoryAction
{
    public function __construct(
        private ExpenseCategory $expenseCategory
    )
    {
        
    }
    public function execute(array $updateExpenseCategoryRecordOptions)
    {
        $expenseCategoryId = $updateExpenseCategoryRecordOptions['id'];
        $data = $updateExpenseCategoryRecordOptions['data'];

        return $this->expenseCategory->where([
            'id' => $expenseCategoryId
        ])->update($data);
    }
}