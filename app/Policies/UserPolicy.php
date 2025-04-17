<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\JsonResponse;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN;
    }

    public function create(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN;
    }

    public function update(User $user, User $editableUser): bool
    {
        return $user->role === RoleEnum::ADMIN && $user->company_id === $editableUser->company_id;
    }

    public function delete(User $user, User $deletableUser): bool
    {
        return $user->role === RoleEnum::ADMIN && $user->company_id === $deletableUser->company_id;
    }
} 