<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Expense extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

  
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function boot()
    {
        parent::boot();

        static::updating(function ($expense) {
            Log::info('Expense updating', ['id' => $expense->id, 'user' => auth()->id()]);
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => $expense->company_id,
                'action' => 'update_expense',
                'changes' => json_encode([
                    'old' => $expense->getOriginal(),
                    'new' => $expense->getAttributes()
                ])
            ]);
        });

        static::deleting(function ($expense) {
            Log::info('Expense deleting', ['id' => $expense->id, 'user' => auth()->id()]);
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => $expense->company_id,
                'action' => 'delete_expense',
                'changes' => json_encode(['old' => $expense->getOriginal()])
            ]);
        });
    }
}