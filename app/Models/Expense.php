<?php

namespace App\Models;

use App\Observers\ExpenseObserver;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    use Auditable;

    protected $guarded = [];
    protected $fillable = ['title', 'amount', 'category', 'user_id', 'company_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

// protected static function booted()
// {
//     static::observe(ExpenseObserver::class);
// }
}