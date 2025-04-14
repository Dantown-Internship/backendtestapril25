<?php

namespace App\Models;

class Company extends AbstractModel
{
    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class, 'company_id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'company_id');
    }
}
