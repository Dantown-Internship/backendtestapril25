<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'model_type',
        'model_id',
        'changes',
        'performed_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'changes' => 'array', // Cast JSON to array
        'performed_at' => 'datetime',
    ];
}
