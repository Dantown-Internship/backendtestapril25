<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Deleted User',
        ]);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->withDefault([
            'name' => 'Deleted Company',
        ]);
    }
}
