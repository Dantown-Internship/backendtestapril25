<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\BelongsToCompany;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    //
    use BelongsToCompany, Auditable, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected $guarded = [];

    #[Scope]
    protected function myOwn(Builder $query): void
    {
        $query->where('user_id', auth()->id());
    }
}
