<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\Dtos\UserDto;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Requests\UserRequest;
use App\Modules\User\Services\UserService;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use ApiResponsesTrait;
    public function __construct(private readonly UserService $userService) {}

    public function create(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $validatedData = $request->validated();

        $validatedData['company_id'] = $request->user()->company_id;

        $response = $this->userService->create(UserDto::fromArray($validatedData));

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

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $response = $this->userService->list($request);

        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_OK
        );
    }

    public function edit(UpdateUserRequest $request, User $id)
    {
        $user = $id;
        $this->authorize('update', $user);

        $validatedData = $request->validated();

        $response = $this->userService->update($user, $validatedData);

        if ($response['status'] === false) {
            return $this->errorApiResponse(
                'An error has occurred, please try again.',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $this->successApiResponse(
            $response['message'],
            $response['data'],
            Response::HTTP_OK
        );
    }
}
