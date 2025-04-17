<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory, SoftDeletes;
    protected static function booted()
    {
        static::updating(function ($expense) {
            $original = $expense->getOriginal();
            $changes = $expense->getDirty();
            if (!empty($changes)) {
                $changeSet = [];
                foreach ($changes as $key => $newValue) {
                    $changeSet[$key] = [
                        'old' => $original[$key] ?? null,
                        'new' => $newValue
                    ];
                }
                Audit::create([
                    'user_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'action' => 'update Expense',
                    'changes' => json_encode($changeSet),
                ]);
            }
        });
        static::deleting(function ($expense) {
            $original = $expense->getOriginal();
    
            Audit::create([
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'action' => 'delete',
                'changes' => json_encode([
                    'deleted_data' => $original
                ]),
            ]);
        });
    }

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'amount',
        'title',
        'category'
    ];
    protected $hidden = [
        'company_id',
        'user_id',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
