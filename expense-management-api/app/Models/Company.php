<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    protected $fillable = ['name', 'email'];

    use HasFactory;
    // A company has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A company has many expenses
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
