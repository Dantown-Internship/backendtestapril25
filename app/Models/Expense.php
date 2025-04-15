<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category'
    ];

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompanyScope($query)
    {
        return $query->where('company_id', auth()->user()->company_id);
    }
}
