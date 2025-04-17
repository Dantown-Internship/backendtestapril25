<?php

namespace App\Services\Users;

use App\Models\User;
use App\Contracts\UserInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use App\Services\Auth\RoleService;



class UserService implements UserInterface
{

    public function __construct(
        public RoleService $roleService
    ) {}


    public function create(array $data, string $roleName): User
    {

        $role =  $this->roleService->getRoleByName($roleName);
        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'company_id' => $data['company_id'],
            'status'     => 'active',
            'role_id'    => $role->id

        ]);

        logAudit(
            userId: auth()->id() ?? $user->id,
            companyId: $user->company_id,
            action: 'create_user',
            changes: ['created' => ['name' => $user->name, 'email' => $user->email, 'role' => $roleName]]
        );

        return $user;
    }



    public function users(int $perPage = 10): LengthAwarePaginator
    {
        return User::with('role')->paginate($perPage);
    }


    public function user(string $userId): User
    {

        return User::with('role')->where('id', $userId)->firstOrFail();
    }

    public function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update([
            'name'       => $data['name'] ?? $user->name,
            'email'      => $data['email'] ?? $user->email,
            'password'   => isset($data['password']) ? Hash::make($data['password']) : $user->password,
            'company_id' => $data['company_id'] ?? $user->company_id,
            'role_id'    => $data['role_id'] ?? $user->role_id,
            'status'     => $data['status'] ?? $user->status,
        ]);

        return $user->refresh();
    }



    public function delete(string $userId): bool
    {
        $user = User::where('id', $userId)->first();
        if (!$user) {
            return false;
        }
        return $user->delete();
    }
}
