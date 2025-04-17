<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 
    
    protected $fillable = ['name', 'email', 'password', 'company_id', 'role'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}

