<?php

namespace App\Http\Controllers;

use App\Actions\GenerateTokenAction;
use App\Actions\RegisterAction;
use App\Data\LoginData;
use App\Data\RegisterData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterData $data): JsonResponse
    {
        $user = app(RegisterAction::class)->execute($data);

        $tokenPayload = (new GenerateTokenAction())->execute($user);

        return new JsonResponse(
            [
                'message' => 'User Registered Successfully',
                'data' => $tokenPayload,
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $loginData = LoginData::fromRequest($request);

        $tokenPayload = (new GenerateTokenAction())->execute($loginData->user);

        return new JsonResponse(
            [
                'message' => 'User Logged In Successfully',
                'data' => $tokenPayload,
            ],
            Response::HTTP_OK
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse(
            [
                'message' => 'User Logged Out Successfully',
            ],
            Response::HTTP_OK
        );
    }
}
