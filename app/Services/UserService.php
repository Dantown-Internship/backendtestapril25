<?php

namespace App\Services;

use App\Models\User;

class UserService {

    public function createUser($data)
    {
        return  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $data['role'],
            'company_id' => $data['company_id'],
        ]);
    }

    public function updateUser(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user->update($data);
        return $user;
    }

 
}