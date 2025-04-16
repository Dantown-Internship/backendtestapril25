<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'amount',
        'title',
        'category',
    ];

    protected $with = [
        'user',
        'company',
    ];
    
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
