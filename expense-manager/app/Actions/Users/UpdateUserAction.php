<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;

class UpdateUserAction
{
    public function handle($id, $validated)
    {
        $user = User::findOrFail($id);
        if ($user->company_id != Auth::user()->company_id) {
            abort(404, 'Unauthorised');
        }
        $user->update($validated);

        // Clear users cache for this company
        CacheService::clearCompanyCache('users');

        return $user;
    }
}
