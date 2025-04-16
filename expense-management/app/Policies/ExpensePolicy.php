<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;
class ExpensePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Expense $expense): Response
    {
        if ($user->company_id !== $expense->company_id) {
            return Response::deny('Cross-company operations forbidden', 403);
        }
        return ($user->isAdmin() || $user->isManager()) 
            ? Response::allow()
            : Response::deny('You are not have the required privileges');
    }

    public function delete(User $user, Expense $expense): Response
    {
        if ($user->company_id !== $expense->company_id) {
            return Response::deny('Cross-company operations forbidden', 403);
        }

        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('You are not have the required privileges');
    }
}
