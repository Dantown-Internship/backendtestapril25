<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, HasUlids;

    protected $fillable =[
        'name',
        'email',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function expenses():HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function auditLogs():HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
