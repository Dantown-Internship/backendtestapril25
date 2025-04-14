<?php

namespace App\Models;

class Company extends AbstractModel
{
    public function users()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'company_id');
    }
}
