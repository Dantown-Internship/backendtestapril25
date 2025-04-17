<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category'
    ];

    // An expense belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An expense belongs to a company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
