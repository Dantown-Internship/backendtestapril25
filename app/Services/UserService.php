<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{

    use HttpResponses;

    public function __construct()
    {
        //
    }


    public function users()
    {
        $user =  User::latest()->paginate(10);
        return  $this->success($user, 'User retrieved successfully');
    }

    public function store($data)
    {
        $companyId = Auth::user()->company_id;
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $companyId,
            'role' => $data['role'],
        ]);

        return $this->success(new UserResource($user), 'User created successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'sometimes|required|in:Admin,Manager,Manager',
        ]);

        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return $this->success(new UserResource($user), 'User role updated successfully');
    }

}
