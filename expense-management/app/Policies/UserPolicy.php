<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $authUser): Response
    {
        return $authUser->isAdmin() ? Response::allow()
        : Response::deny('You do not have the required privileges');

    }

    public function update(User $authUser, User $targetUser): Response
    {
        if ($authUser->company_id !== $targetUser->company_id) {
            return Response::deny('Cross-company operations forbidden', 403);
        }
    
        return $authUser->isAdmin() 
            ? Response::allow()
            : Response::deny('You do not have the required privileges');
    }

    public function create(User $authUser): Response
    {
        return $authUser->isAdmin() ? Response::allow()
        : Response::deny('You do not have the required privileges');

    }
}
