<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;

class DeleteUserAction
{
    public function handle($id)
    {
        $user = User::findOrFail($id);

        // Check if user belongs to the same company
        if ($user->company_id != Auth::user()->company_id) {
            abort(404, 'Unauthorised');
        }

        $user->delete();

        // Clear users cache for this company
        CacheService::clearCompanyCache('users');
    }
}
