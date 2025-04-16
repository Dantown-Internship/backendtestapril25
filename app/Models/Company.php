<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Company extends Model
{
    use HasFactory;
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
