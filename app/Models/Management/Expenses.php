<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Expenses extends Model
{
    use HasFactory, HasUuids;


    protected $keyType = 'string'; 
    public $incrementing = false; 

    protected $fillable = [
        'user_id',
        'company_id',
        'title',
        'category',
        'amount',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
