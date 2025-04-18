<?php

// app/Models/Tenant/Expense.php
namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['user_id', 'title', 'amount', 'category'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}