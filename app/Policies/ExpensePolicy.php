<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): Response
    {
        return match($user->role) {
            Role::Admin, Role::Manager => Response::allow(),
            Role::Employee => $expense->user_id === $user->id
                ? Response::allow()
                : Response::denyWithStatus(404),
        };
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        return match($user->role) {
            Role::Admin, Role::Manager => true,
            Role::Employee => false,
        };
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->role === Role::Admin;
    }

}
