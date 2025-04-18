<?php

// app/Models/Tenant/User.php
namespace App\Models\Tenant;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    protected $connection = 'tenant';
    protected $fillable = ['central_user_id', 'name', 'email', 'password', 'role'];

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