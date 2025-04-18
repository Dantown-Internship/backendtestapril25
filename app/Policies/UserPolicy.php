<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $targetUser): bool
    {
        return  $user->role === User::$Admin  && $user->company_id === $targetUser->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        //
    }

  
}
