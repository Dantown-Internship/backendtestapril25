<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'changes'
    ];
}
