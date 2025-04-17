<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface
{
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function updateUser(array $data, User $user): bool
    {
        return $user->update($data);
    }
}

