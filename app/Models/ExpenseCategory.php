<?php

namespace App\Models;

class ExpenseCategory extends AbstractModel
{
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}
