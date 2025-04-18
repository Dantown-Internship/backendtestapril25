<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'amount',
        'category',
    ];

    /**
     * Define relationships.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booted method for audit logging.
     */
    protected static function booted()
    {
        static::updating(function ($expense) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => $expense->company_id,
                'action' => 'update',
                'changes' => [
                    'old' => $expense->getOriginal(),
                    'new' => $expense->getAttributes(),
                ],
            ]);
        });

        static::deleting(function ($expense) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'company_id' => $expense->company_id,
                'action' => 'delete',
                'changes' => $expense->getAttributes(),
            ]);
        });
    }
}
