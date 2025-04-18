<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Constant;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        // Restrict access to Admins
        if ($admin->role !== 'Admin') {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        $perPage = $request->input('per_page', 10);

        $users = User::with(['company'])
            ->where('company_id',$admin->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $admin = $request->user();

        // Restrict access to Admins
        if ($admin->role !== 'Admin') {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        // Validation rules
        $rules = [
            'firstname'=> 'required|string|max:255',
            'lastname'=> 'required|string|max:255',
            'email'=> 'required|email|unique:users,email',
            'password'=> 'required|min:6',
            'role'=> 'required|in:Manager,Employee',
        ];

        $messages = [
            'firstname.required'=> 'First name is required.',
            'firstname.string'=> 'First name must be a string.',
            'firstname.max'=> 'First name must not be more than 255 characters.',

            'lastname.required'=> 'Last name is required.',
            'lastname.string'=> 'Last name must be a string.',
            'lastname.max'=> 'Last name must not be more than 255 characters.',

            'email.required'=> 'Email address is required.',
            'email.email'=> 'Please enter a valid email address.',
            'email.unique'=> 'This email address is already in use.',

            'password.required'=> 'Password is required.',
            'password.min'=> 'Password must be at least 6 characters long.',

            'role.required'=> 'User role is required.',
            'role.in'=> 'Role must be either Manager or Employee.',
        ];

        // Validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Handle failed validation
        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        // Process validated data
        $validatedData = $validator->validated();
        $validatedData['company_id'] = $admin->company_id;
        $validatedData['password']   = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return (new UserResource($user))->additional([
            'company' => $admin->company->name
        ]);
    }

    public function update(Request $request, $id)
    {
        $admin = $request->user();

        // Restrict access to Admins
        if ($admin->role !== 'Admin') {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        try {
            // Ensure the user to update belongs to the same company as the Admin
            $user = User::with(['company'])->where('id', $id)
                ->where('company_id', $admin->company_id)
                ->firstOrFail();
        } catch (\Exception $exception){
            return response()->apiError(Constant::RESOURCE_NOT_FOUND,$exception->getMessage(),404);
        }

        // Validation rules
        $rules = [
            'firstname'=> 'nullable|string|max:255',
            'lastname'=> 'nullable|string|max:255',
            'email'=> ['nullable','email', Rule::unique('users','email')->ignore($user->id)],
            'password'=> 'nullable|min:6',
            'role'=> 'nullable|in:Manager,Employee',
        ];

        $messages = [
            'firstname.string'=> 'First name must be a string.',
            'firstname.max'=> 'First name must not be more than 255 characters.',

            'lastname.string'=> 'Last name must be a string.',
            'lastname.max'=> 'Last name must not be more than 255 characters.',

            'email.email'=> 'Please enter a valid email address.',
            'email.unique'=> 'This email address is already in use.',

            'password.min'=> 'Password must be at least 6 characters long.',

            'role.in'=> 'Role must be either Manager or Employee.',
        ];

        // Validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Handle failed validation
        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        $validatedData = $validator->validated();

        if (isset($validatedData['firstname'])) $user->firstname = $validatedData['firstname'];
        if (isset($validatedData['lastname']))  $user->lastname = $validatedData['lastname'];
        if (isset($validatedData['email']))     $user->email = $validatedData['email'];
        if (isset($validatedData['password']))  $user->password = Hash::make($validatedData['password']);
        if (isset($validatedData['role']))      $user->role = $validatedData['role'];

        return new UserResource($user);

    }
}
