<?php

namespace App\Models;

use App\Libs\Traits\BelongsToContext;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];

    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }
}
