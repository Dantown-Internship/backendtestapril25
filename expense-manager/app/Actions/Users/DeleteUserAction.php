<?php

namespace App\Actions\Users;

use App\Models\User;

class DeleteUserAction
{
    public function handle($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
