<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        // Api resource can be use here
        return User::where('company_id', $request->user()->company_id)
            ->with('company')
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->only(['name', 'email', 'role']);
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:Admin,Manager,Employee',
        ], [
            'role.in' => 'The selected role is invalid. Kindly, select between Admin, Manager or Employee'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $password = \Str::ulid();
        $user = User::create([
            'company_id' => $request->user()->company_id,
            'password' => $password,
            ...$data
        ]);

        // Welcome email with reset password link should be sent here
        return $this->successResponse('User created successfully.', $user->load('company'), 201);
    }

    public function update(Request $request, User $user)
    {
        if ($user->company_id !== $request->user()->company_id) {
            return $this->errorResponse('Not found', 404);
        }

        $data = $request->only(['name', 'email', 'role']);
        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'sometimes|in:Admin,Manager,Employee',
        ], [
            'role.in' => 'The selected role is invalid. Kindly, select between Admin, Manager or Employee'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $user->update($data);
        return $this->successResponse('User updated successfully.', $user->load('company'), 200);
    }
}
