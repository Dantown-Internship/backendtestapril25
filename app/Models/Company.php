<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasUuids,HasFactory;
    protected $fillable = ['name', 'email'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
