<?php

namespace App\Models;

class ExpenseCategory extends AbstractModel
{
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}
