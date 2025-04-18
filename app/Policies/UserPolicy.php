<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
   
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $targetUser)
    {
        // Only allow if both users belong to the same company and auth user is an Admin
        return $authUser->company_id === $targetUser->company_id;
    }
}
