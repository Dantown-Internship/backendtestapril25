<?php

namespace App\Models;

class Expense extends AbstractModel
{
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
