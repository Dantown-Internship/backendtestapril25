<?php

namespace App\Queries;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthQuery
{


    public function create(array $data, string $roleId): User
    {
        return User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'company_id' => $data['company_id'],
            'status'     => 'active',
            'role_id'    => $roleId,
        ]);
    }


    public function delete(string $userId): bool
    {
        $user = User::findOrFail($userId);
        return $user->delete();
    }

}
