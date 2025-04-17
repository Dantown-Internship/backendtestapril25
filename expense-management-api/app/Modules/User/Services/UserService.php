<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\Auth\Resources\UserResource;
use App\Modules\User\Dtos\UserDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserService
{

    public function create(UserDto $dto)
    {
        try {
            $userData = (array) $dto;

            $userData['password'] = Hash::make($userData['password']);

            $user = User::create($userData);

            return [
                'status' => true,
                'message' => 'User created Successfully',
                'data' => new UserResource($user),
            ];
        } catch (Throwable $th) {
            logger()->error('Error while creating user', [$th]);

            return null;
        }
    }

    public function list(Request $request)
    {
        try {

            // Get users only from the current user's company
            $users = User::where('company_id', $request->user()->company_id)
                ->paginate(10);

            $result = [
                'status' => true,
                'message' => 'Users retrieved successfully',
                'data' => UserResource::collection($users),
            ];

            return $result;
        } catch (Throwable $th) {
            logger('Error while listing users', [$th]);
            return null;
        }
    }

    public function update(User $user, array $validatedData)
    {
        $user->update($validatedData);
        return [
            'status' => true,
            'message'=> 'User successfully updated',
            'data' => [
                'user' => new UserResource($user)
            ]
        ];
        
    }
}
