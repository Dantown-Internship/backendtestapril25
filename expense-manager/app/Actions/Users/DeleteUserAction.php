<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserAction
{
    public function handle($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}
