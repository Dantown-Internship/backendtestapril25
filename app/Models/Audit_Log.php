<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit_Log extends Model
{
    protected $table= "audit_logs";
    protected $casts = [
        'changes' => 'array',
    ];

    protected $fillable = ['user_id', 'company_id', 'action', 'changes'];

    
}


