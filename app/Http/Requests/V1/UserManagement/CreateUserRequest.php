<?php

namespace App\Http\Requests\V1\UserManagement;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'between:1,200'],
            'email' => ['required', 'string', 'between:1,200', 'unique:users,email'],
            'role' => ['required', 'string', 'in:Admin,Manager,Employee'],
            'password' => ['required', 'string', 'between:8,20', 'confirmed']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The user name is required.',
            'name.string' => 'The user name must be a string.',
            'name.between' => 'The user name must be between 1 and 200 characters.',

            'email.required' => 'The user email is required.',
            'email.string' => 'The user email must be a valid string.',
            'email.between' => 'The user email must be between 1 and 200 characters.',
            'email.unique' => 'The user email has already been taken.',

            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a string.',
            'password.between' => 'The password must be between 8 and 20 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            
            'role.required' => 'The role is required',
            'role.string' => 'The role must be a string',
            'role.in' => 'The role must be either Admin, Manager Or Employee',
        ];
    }
}
