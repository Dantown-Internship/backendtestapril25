<?php

namespace App\Models;

use App\Libs\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'company_id',
        'user_id',
        'amount',
        'description',
        'created_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLoggableAttributes(): array
    {
        return ['updated', 'deleted'];
    }
}
