<?php

namespace App\Services\Users;

use App\Models\User;
use App\Contracts\UserInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Services\Auth\RoleService;
use App\Queries\AuthQuery;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;



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
        $companyId = auth()->user()->company_id ?? 'default';
        $cacheKey = "users_{$companyId}_{$perPage}";

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            fn () => $this->authQuery->users($perPage)
        );
    }


    public function user(string $userId): User
    {
        return $this->authQuery->user($userId);  
    }



    public function update(string $userId, array $data): User
    {
        $roleName = $data['role_name'] ?? throw new InvalidArgumentException('Role name is required');
        $role = $this->roleService->getRoleByName($roleName);

        $user = User::findOrFail($userId);
        $oldRoleName = $user->role_id ? $this->roleService->getRoleById($user->role_id)->name : null;

        $user = $this->authQuery->updateUserRole($userId, $role->id);

        logAudit(
            userId: auth()->id() ?? $user->id,
            companyId: $user->company_id,
            action: 'update_user_role',
            changes: [
                'old' => ['role_name' => $oldRoleName],
                'new' => ['role_name' => $roleName]
            ]
        );

      
        Cache::forget("users_{$user->company_id}");

        $this->roleService->assignRole($user, $roleName);

        return $user;
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
