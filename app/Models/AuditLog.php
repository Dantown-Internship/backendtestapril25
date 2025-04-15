<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    //
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = ['company_id', 'user_id', 'changes', 'action'];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

}
