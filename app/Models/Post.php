<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic',
        'content',
        'images',
        'tenant_id',
    ];

    public function tenant(){
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
