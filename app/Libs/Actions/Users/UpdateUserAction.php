<?php

namespace App\Libs\Actions\Users;

use App\Models\User;

class UpdateUserAction
{
    public function handle($request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
            'success' => true
        ], 200);
    }
}