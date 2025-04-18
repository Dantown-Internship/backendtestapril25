<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function updateUser(int $id, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::find($id);
        $user->update($data);

        return $user;
    }

    public function getUsersByCompany(int $companyId, $perPage)
    {
        $perPage = $perPage ?? 15;
        return User::with('company')->where('company_id', $companyId)->paginate($perPage);
    }

    public function find(int $id): User
    {
        $user = User::find($id);

        return $user;
    }
}
