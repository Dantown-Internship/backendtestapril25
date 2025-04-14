<?php

namespace App\Http\Controllers\V1\Admin\UserManagement;

use App\Actions\User\GetUserByIdAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\UserManagement\UpdateUserRequest;

class UpdateUserController extends Controller
{
    public function __construct(
        private GetUserByIdAction $getUserByIdAction,
        private UpdateUserAction $updateUserAction
    ) {}

    public function __invoke(UpdateUserRequest $request, string $userId)
    {
        $loggedInUser = auth('sanctum')->user();

        $user = $this->getUserByIdAction->execute($userId);

        if (is_null($user)) {
            return generateErrorApiMessage('User record does not exists', 404);
        }

        $updateUserPayload = $request->validated();

        $this->updateUserAction->execute([
            'id' => $userId,
            'data' => $updateUserPayload
        ]);

        return generateSuccessApiMessage('User was updated successfully');
    }
}
