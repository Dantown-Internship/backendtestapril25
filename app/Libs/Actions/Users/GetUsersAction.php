<?php

namespace App\Libs\Actions\Users;

use App\Models\User;

class GetUsersAction
{
    public function handle($request)
    {
        $users = User::paginate(RESULT_COUNT);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users,
            'success' => true
        ], 200);
    }
}