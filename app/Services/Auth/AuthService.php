<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\RoleService;
use App\Queries\AuthQuery;
use App\Contracts\AuthInterface;
use Illuminate\Auth\AuthenticationException;

class AuthService implements AuthInterface
{

    public function __construct(
        public RoleService $roleService,
        protected AuthQuery $authQuery
    ) {}


    public function signup(array $data, string $roleName): User
    {

        $role =  $this->roleService->getRoleByName($roleName);
        
        $user =  $this->authQuery->create($data, $role->id);

        $this->roleService->assignRole($user, $roleName);

        logAudit(
            userId: auth()->id() ?? $user->id,
            companyId: $user->company_id,
            action: 'create_user',
            changes: ['created' => ['name' => $user->name, 'email' => $user->email, 'role' => $roleName]]
        );

        return $user;
    }



    public function signin(array $credentials): array
    {

        if (!auth()->attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials!');
        }

        if (auth()->user()->status !== 'active') {
            auth()->logout();
            throw new AuthenticationException('Account is not active.');
        }
        $user = auth()->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Authenticated!',
            'data'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'company_id' => $user->company_id,
                'role_name' => $user->role->name,
                'status'    => $user->status,
                'token'     => $token
            ],
        ];
    }

    public function me(): User
    {
        return auth()->user();
    }


    public function logout(): bool
    {
        auth()->logout();
        return true;
    }
}
