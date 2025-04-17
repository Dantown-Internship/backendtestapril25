<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    use HasUuids, HasFactory;

    protected $keyType = 'string'; 
    public $incrementing = false; 

    protected $fillable = ['id','name'];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
