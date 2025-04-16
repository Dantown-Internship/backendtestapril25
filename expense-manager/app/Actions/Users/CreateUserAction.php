<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function handle(array $validated)
    {
        $admin = Auth::user();

        $validated['company_id'] = $admin->company_id;
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        // Clear users cache for this company
        CacheService::clearCompanyCache('users');

        return $user;
    }
}
