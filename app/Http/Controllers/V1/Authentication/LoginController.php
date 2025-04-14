<?php

namespace App\Http\Controllers\V1\Authentication;

use App\Actions\User\GetUserByEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Authentication\LoginRequest;
use App\Http\Resources\V1\Authentication\LoginResource;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct(
        private GetUserByEmailAction $getUserByEmailAction
    ) {}

    public function __invoke(LoginRequest $request)
    {
        $relationships = ['company'];

        $user = $this->getUserByEmailAction->execute($request->email, $relationships);

        if (is_null($user)) {
            return generateErrorApiMessage('Invalid login credentials', 400);
        }

        if (Hash::check($request->password, $user->password) === false) {
            return generateErrorApiMessage('Invalid login credentials', 400);
        }


        $responsePayload = new LoginResource($user);

        return generateSuccessApiMessage('Logged in successfully', 200, $responsePayload);
    }
}
