<?php

namespace App\Models\Logging;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory, HasUuids;


    protected $keyType = 'string'; 
    public $incrementing = false; 

    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'changes',
    ];

}
