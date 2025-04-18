<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    
    public function isAdmin(): bool
    {
        return strtolower($this->role) === 'admin';
    }

    
    public function isManager(): bool
    {
        return strtolower($this->role) === 'manager';
    }

    
    public function isEmployee(): bool
    {
        return strtolower($this->role) === 'employee';
    }
}
