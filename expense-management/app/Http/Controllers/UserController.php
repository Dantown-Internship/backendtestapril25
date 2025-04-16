<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\User\CreateRequest;
use App\Models\User;


class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository
    )
    {}
    
    public function view(): JsonResponse
    {
        $this->authorize('view', auth()->user());
        $users = $this->userRepository->get(
            auth()->user()->company_id,
        );
            
        return $this->respond('Users retrieved', $users->toArray());
    }
    
    public function create(CreateRequest $request): JsonResponse
    {
        $this->authorize('create', auth()->user());
        
        $user = $this->userRepository->create($request);

        return $this->respond('User created successfully', $user->toArray(), statusCode:Response::HTTP_CREATED);
    }
    
    public function update(Request $request, User $user): JsonResponse
    {
        if ($user->company_id !== auth()->user()->company_id) {
            return $this->respond('Unauthorized', statusCode: Response::HTTP_UNAUTHORIZED);
        }
        
        $request->validate([
            'role' => 'required|in:Admin,Manager,Employee'
        ]);
        

        $updatedUser = $this->userRepository->update(
            $user,
            $request->only(['role'])
        );
        
        return $this->respond('User updated', $updatedUser->toArray());
    }
}
