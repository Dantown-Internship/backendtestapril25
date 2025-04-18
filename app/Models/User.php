<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
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
        'role',
        'company_id',

    ];

    public static $Admin = 'Admin';

    public function company(){
        return $this->belongsTo(Company::class, 'user_id');
    }

    public function expenses(){
        return $this->hasMany(Expense::class, 'user_id');
    }


    public static function getUserList($user){
        return User::where('company_id', $user->company_id)->where('role', User::$Admin)->get();
    }


    public static function createRecord($params, $companyId){

        $addUser = new User();
        $addUser->name= $params['name'];
        $addUser->email = $params['email'];
        $addUser->phone = $params['phone'];
        $addUser->password  = Hash::make($params['password']);
        $addUser->role = $params['role'];
        $addUser->company_id = $companyId;
        $addUser->save();
        return $addUser;
    }
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
}
