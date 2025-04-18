<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $fillable = [
        "title",
        "amount",
        "category"
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function company() {
        return $this->belongsTo(Company::class);
    }
    
}
