<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRoleEnum;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // 'role' => UserRoleEnum::class
        ];
    }


    public function company(){
        return $this->belongsTo(Companies::class);
    }

    public function expenses(){
        return $this->hasMany(Expenses::class);
    }

    // create helper methods
    public function isAdmin(){
        return $this->role === UserRoleEnum::Admin->name;
    }

    public function isManager(){
        return $this->role === UserRoleEnum::Manager->name;
    }

    public function isEmployee(){
        return $this->role === UserRoleEnum::Employee->name;
    }
}
