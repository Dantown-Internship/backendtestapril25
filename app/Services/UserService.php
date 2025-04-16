<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUsersByCompany(User $user)
    {
        return $user->company->users()->orderBy('name', 'asc');
    }

    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $user;
    }

    public function getUserByEmail(string $email)
    {
        $user = User::where('email', $email)->first();

        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);

        return $user;
    }
}
