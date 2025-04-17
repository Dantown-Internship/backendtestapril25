<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    //
    protected $fillable = ['user_id', 'company_id', 'action', 'changes'];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
