<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        return $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can manage the company.
     */
    public function manage(User $user, Company $company): bool
    {
        return $user->company_id === $company->id &&
               $user->role === 'Admin';
    }
}
