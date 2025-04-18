<?php

namespace App\Models;

use App\Http\Filter\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    protected $guarded = [];
    protected static function booted(): void
    {
        static::addGlobalScope('company', function (Builder $query) {
            if (auth()->hasUser()) {
                // $query->where('company_id', auth()->user()->company_id);
                // or with a `team` relationship defined:
                $query->whereBelongsTo(auth()->user()->company);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeFilter(Builder $builder, QueryFilter $filters) {
        return $filters->apply($builder);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
