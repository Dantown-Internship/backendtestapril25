<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $fillable = ["company_name","company_email"];

    public function users() {
        return $this->hasMany(User::class);
    }
    
    public function expenses() {
        return $this->hasMany(Expense::class);
    }
    
}
