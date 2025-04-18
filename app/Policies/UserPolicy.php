<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy
{

    public function store(User $user): bool
    {
        return $user->isAdmin();
    }

    public function index(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Model $model): bool
    {
        return ($user->isAdmin() || $user->id === $model->id) && $user->company_id === $model->company_id;
    }
}
