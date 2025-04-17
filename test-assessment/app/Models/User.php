<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
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
        'role'
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
        'role' => UserRole::class,
    ];

    protected static function booted()
    {
        static::updating(function ($user) {
            $original = $user->getOriginal();
            $changes = $user->getDirty();
            if (!empty($changes)) {
                $changeSet = [];
                foreach ($changes as $key => $newValue) {
                    $changeSet[$key] = [
                        'old' => $original[$key] ?? null,
                        'new' => $newValue
                    ];
                }
                Audit::create([
                    'user_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'action' => 'update User Record',
                    'changes' => json_encode($changeSet),
                ]);
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function expense()
    {
        return $this->hasMany(Expense::class, 'user_id', 'id');
    }
}
