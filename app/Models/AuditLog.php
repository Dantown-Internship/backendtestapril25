<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Termwind\Components\Ul;

class AuditLog extends Model
{
    /** @use HasFactory<\Database\Factories\AuditLogFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = ['company_id', 'user_id', 'action', 'changes', 'created_at'];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }
}
