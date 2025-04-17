<?php

namespace App\Models;

use App\Casts\AuditLogChangesCast;
use App\Enums\AuditLogAction;
use App\Models\Concerns\HasUuid;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy(CompanyScope::class)]
class AuditLog extends Model
{
    use HasFactory, HasUuid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'uuid',
        'company_id',
        'user_id',
        'action',
        'changes',
    ];

    protected $casts = [
        'action' => AuditLogAction::class,
        'changes' => AuditLogChangesCast::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
