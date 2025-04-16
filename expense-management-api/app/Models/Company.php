<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    protected $with = [
        'users',
        'expenses',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
