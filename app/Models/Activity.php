<?php

namespace App\Models;

use App\Libs\Traits\BelongsToContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory, BelongsToContext;

    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'changes',
    ];
    
    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
