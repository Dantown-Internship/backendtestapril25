<?php

// app/Models/Central/User.php
namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    // use HasApiTokens;

    protected $connection = 'central';
    protected $fillable = ['email', 'password', 'tenant_ids', 'role'];
    protected $hidden = ['password'];
    protected $casts = [
        'tenant_ids' => 'array',
    ];

    // public function isSuperAdmin()
    // {
    //     return $this->role === 'SuperAdmin';
    // }
}