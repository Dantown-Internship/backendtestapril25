<?php

namespace App\Services\Users;

use App\Models\User;
use App\Contracts\UserInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use App\Services\Auth\RoleService;
use App\Queries\AuthQuery;



class UserService implements UserInterface
{

    public function __construct(
        public RoleService $roleService,
        protected AuthQuery $authQuery
    ) {}


    public function create(array $data, string $roleName): User
    {

        $role =  $this->roleService->getRoleByName($roleName);

        $user =  $this->authQuery->create($data, $role->id);

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
        $user = User::findOrFail($userId);
        $data = $user->only(['name', 'email', 'role_id']);

        logAudit(
            userId: auth()->id() ?? $user->id,
            companyId: $user->company_id,
            action: 'delete_user',
            changes: ['deleted' => $data]
        );

        return $this->authQuery->delete($userId);
    }
}
