<?php

namespace App\Libs\Actions\Users;

use App\Http\Resources\ExpenseResource;
use App\Models\User;

class UpdateUserAction
{
    public function handle($request, $id): ExpenseResource
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return ExpenseResource::make($user)->additional([
            'message' => 'User updated successfully',
            'success' => true,
        ]);
    }
}