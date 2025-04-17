<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Company extends Model
{

    use HasFactory;
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
        // 'expenses',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function admins()
    {
        return $this->users()->where('role', 'Admin');
    }
}
