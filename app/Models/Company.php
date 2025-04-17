<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasUuids, HasFactory;

    protected $keyType = 'string'; 
    public $incrementing = false; 

    protected $fillable = ['id','name', 'email'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
