<?php

namespace App\Libs\Actions\Users;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\User;
use App\Http\Resources\ExpenseResource;

class GetUsersAction
{
    /**
     * Handle the action to get all users.
     * @param mixed $request
     * @return AnonymousResourceCollection<ExpenseResource>
     */
    public function handle($request): AnonymousResourceCollection
    {
        $users = User::latest()->paginate(RESULT_COUNT);

        return ExpenseResource::collection($users)->additional([
            'message' => 'Users retrieved successfully',
            'success' => true,
        ]);
    }
}