<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any audit logs.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can view the audit log.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->company_id === $auditLog->company_id &&
               in_array($user->role, ['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can manage audit logs.
     */
    public function manage(User $user, Company $company): bool
    {
        return $user->company_id === $company->id &&
               $user->role === 'Admin';
    }
}
