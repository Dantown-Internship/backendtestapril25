<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'category',
        'user_id',
        'company_id'
    ];
    
    public function company() {
        return $this->belongsTo(Company::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
}

