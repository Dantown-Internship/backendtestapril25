<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === RoleEnum::ADMIN;
    }

    public function view(User $user, Company $company): bool
    {
        // Users can only view their own company
        return $user->company_id === $company->id;
    }

    public function create(User $user): bool
    {
        // Only super admins would create companies (not implemented in this system)
        return false;
    }

    public function update(User $user, Company $company): bool
    {
        return $user->role === RoleEnum::ADMIN && $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company): bool
    {
        return false;
    }
} 