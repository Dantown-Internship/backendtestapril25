<?php

namespace App\Http\Controllers\V1\UserManagement;

use App\Actions\User\CreateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\UserManagement\CreateUserRequest;
use App\Http\Resources\V1\UserManagement\GetUserResource;
use Illuminate\Support\Facades\Hash;

class CreateUserController extends Controller
{
    public function __construct(
        private CreateUserAction $createUserAction,
    ) {}

    public function __invoke(CreateUserRequest $request)
    {
        $loggedInUser = auth('sanctum')->user();

        $createUserRecordOptions =  $request->safe()->merge([
            'company_id' => $loggedInUser->company_id,
            'password' => Hash::make($request->password)
        ])->all();

        unset($createUserRecordOptions['password_confirmation']);

        $createdUser = $this->createUserAction->execute(
            $createUserRecordOptions
        );

        $responsePayload = new GetUserResource($createdUser);

        return generateSuccessApiMessage('User was created successfully', 201, $responsePayload);
    }
}
