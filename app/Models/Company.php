<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'company_id');
    }
}
