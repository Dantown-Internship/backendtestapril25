<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasUuids;
    
    protected $fillable = [
        'action',
        'company_id',
        'changes',
        'user_id',
        'created_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'changes' => 'json'
    ];
}
