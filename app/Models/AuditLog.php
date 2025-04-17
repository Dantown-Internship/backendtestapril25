<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'changes',
    ];

    protected $casts = [
        'changes' => 'json',
    ];

    public function scopeAuthCompany($query)
    {
        return $query->where('company_id', Auth::user()->company_id);
    }
}
