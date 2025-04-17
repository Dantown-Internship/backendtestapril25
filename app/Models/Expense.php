<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeAuthCompany($query)
    {
        return $query->where('company_id', Auth::user()->company_id);
    }

    protected function makeAuditLogEntry($expense, $action)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => $action,
            'changes' => [
                'old' => $expense->getOriginal(),
                'new' => $action === 'updated' ? $expense : null,
                'difference' => $action === 'updated' ? $expense->getChanges() : null,
            ],
        ]);
    }

    protected function flushCache($expense)
    {
        $tagKey = "expenses.company.{$expense->company_id}";
        $keys = Cache::get($tagKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($tagKey);
    }

    protected static function booted()
    {
        static::saved(function (Expense $expense) {
            $expense->flushCache($expense);
        });

        static::updated(function (Expense $expense) {
            $expense->makeAuditLogEntry($expense, 'updated');
        });

        static::deleted(function (Expense $expense) {
            $expense->makeAuditLogEntry($expense, 'deleted');
            $expense->flushCache($expense);
        });
    }
}
