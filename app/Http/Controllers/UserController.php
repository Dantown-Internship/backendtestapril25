<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreRequest;
use App\Http\Requests\Users\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    private $authGuard;
    public function __construct(private readonly UserService $userService)
    {
        $this->authGuard = auth('sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        /** @var \App\Models\User $user */
        $user = $this->authGuard->user();
        return UserResource::collection(

            $user->getCompanyUsers()
        )->additional([
            'meta' => [
                'message' => 'Users retrieved successfully',
                'status' => 200,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = $this->userService->createUser([...$validated, 'company_id' => $this->authGuard->user()->company_id]);
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $user)
    {
        $validated = $request->validated();
        $user = $this->userService->updateUser($user, $validated);
        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
