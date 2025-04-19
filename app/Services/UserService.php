<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    // list users (Admin)
    public function listUsers($request)
    {
        // get all user for the authenticated company and paginate
        $users = User::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($request->name, fn($q) => $q->where('name', 'like', "%{$request->name}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $users;
    }

    // create user (Admin)
    public function createUser($request)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        return $user;
    }

    // update user (Admin)
    public function updateUser($request, $id)
    {
        $validated = $request->validated();

        $user = User::findOrFail($id);
        $user->update($validated);

        return $user;
    }

}
