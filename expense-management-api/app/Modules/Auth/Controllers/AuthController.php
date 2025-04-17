<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Dtos\RegisterDto;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Services\AuthService;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    use ApiResponsesTrait;

    public function __construct(private readonly AuthService $authService) {}
   
    public function store(RegisterRequest $request): JsonResponse
    {

        $validatedData = $request->validated();

        $response = $this->authService->create(RegisterDto::fromArray($validatedData));

        if (!$response) {
            return $this->errorApiResponse(
                'An error has occurred. please try again later',
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_CREATED
        );
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();
        $response = $this->authService->auth($validatedData);

        if ($response['status'] === false){
            return $this->errorApiResponse(
                $response['message'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        };

        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_ACCEPTED
        );
    }
}
