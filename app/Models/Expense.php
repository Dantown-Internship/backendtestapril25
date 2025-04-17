<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Scopes\CompanyScope;
use App\Observers\ExpenseObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ExpenseObserver::class)]
#[ScopedBy(CompanyScope::class)]
class Expense extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'company_id',
        'user_id',
        'title',
        'amount',
        'category',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
