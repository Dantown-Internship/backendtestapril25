<?php

namespace App\Queries;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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


    public function users(int $perPage = 10): LengthAwarePaginator
    {
        return User::with('role')->paginate($perPage);
    }

    public function user(string $userId): User
    {
        return User::with('role')->where('id', $userId)->firstOrFail();
    }

    
    public function updateUserRole(string $userId, string $roleId): User
    {
        $user = User::findOrFail($userId);
        $user->update(['role_id' => $roleId]);
        return $user->refresh();
    }


    public function delete(string $userId): bool
    {
        $user = User::findOrFail($userId);
        return $user->delete();
    }

}
