<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'amount',
        'title',
        'category',
        'company_id',
        'user_id'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function scopeSearch($query, $term) {
        return $query->where('title', 'like', "%$term%")
                     ->orWhere('category', 'like', "%$term%");
    }

}
