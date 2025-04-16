<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function expenses() {
        return $this->hasMany(Expense::class);
    }

    public function auditLogs()
{
    return $this->hasMany(AuditLog::class);
}


}
