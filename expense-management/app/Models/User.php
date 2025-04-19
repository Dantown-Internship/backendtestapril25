<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes;
    
    protected $fillable = ['name', 'email', 'password', 'company_id', 'role'];
    protected $hidden = ['password','deleted_at'];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    
    public function isAdmin()
    {
        return $this->role === 'Admin';
    }
    
    public function isManager()
    {
        return $this->role === 'Manager';
    }
}
