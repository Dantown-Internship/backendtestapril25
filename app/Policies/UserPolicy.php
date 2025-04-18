<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function before(User $user, string $ability): bool|null
{
    // Ensure company is fetched correctly based on the user's company_id
    $company = Company::find($user->company_id);

    // Check if the user is a superadmin
    if ($user->role === 'SuperAdmin') {
        return true;  // superAdmins can do everything
    }

    // Admins are allowed only if their associated company matches the provided company
    if ($user->role === 'Admin' && $company && $company->id === $user->company_id) {
        return true;  // Admin is authorized
    }

    // Otherwise, continue with other policy methods
    return null;  // Proceed to other checks for this ability
   }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
       //return true;
      return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return auth()->user()->role === "Admin" && auth()->user()->company_id === $model->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
