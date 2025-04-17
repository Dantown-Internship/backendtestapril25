<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function isAdmin()
    {
        return $this->role === 'Admin';
    }

    public function isManager()
    {
        return $this->role === 'Manager';
    }

    public function isEmployee()
    {
        return $this->role === 'Employee';
    }

    public function scopeAuthCompany($query)
    {
        return $query->where('company_id', Auth::user()->company_id);
    }

    protected function flushCache($user)
    {
        $tagKey = "users.company.{$user->company_id}";
        $keys = Cache::get($tagKey, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget($tagKey);
    }

    protected static function booted()
    {
        static::saved(function (User $user) {
            $user->flushCache($user);
        });

        static::deleted(function (User $user) {
            $user->flushCache($user);
        });
    }
}
