<?php

namespace App\Actions\Expense;

use App\Models\Expense;

class UpdateExpenseAction
{
    public function __construct(
        private Expense $expense
    )
    {
        
    }
    public function execute(array $updateExpenseRecordOptions)
    {
        $expenseId = $updateExpenseRecordOptions['id'];
        $data = $updateExpenseRecordOptions['data'];

        return $this->expense->where([
            'id' => $expenseId
        ])->update($data);
    }
}