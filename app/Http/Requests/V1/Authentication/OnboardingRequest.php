<?php

namespace App\Http\Requests\V1\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class OnboardingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company.name' => ['required', 'string', 'between:1,200'],
            'company.email' => ['required', 'string', 'email', 'between:1,200', 'unique:companies,email'],
            'user.name' => ['required', 'string', 'between:1,200'],
            'user.email' => ['required', 'string', 'between:1,200', 'unique:users,email'],
            'user.password' => ['required', 'string', 'between:8,20', 'confirmed']
        ];
    }

    public function messages(): array
    {
        return [
            'company.name.required' => 'The company name is required.',
            'company.name.string' => 'The company name must be a string.',
            'company.name.between' => 'The company name must be between 1 and 200 characters.',

            'company.email.required' => 'The company email is required.',
            'company.email.string' => 'The company email must be a valid string.',
            'company.email.email' => 'The company email must be a valid email address.',
            'company.email.between' => 'The company email must be between 1 and 200 characters.',
            'company.email.unique' => 'The company email has already been taken.',

            'user.name.required' => 'The user name is required.',
            'user.name.string' => 'The user name must be a string.',
            'user.name.between' => 'The user name must be between 1 and 200 characters.',

            'user.email.required' => 'The user email is required.',
            'user.email.string' => 'The user email must be a valid string.',
            'user.email.between' => 'The user email must be between 1 and 200 characters.',
            'user.email.unique' => 'The user email has already been taken.',

            'user.password.required' => 'The password is required.',
            'user.password.string' => 'The password must be a string.',
            'user.password.between' => 'The password must be between 8 and 20 characters.',
            'user.password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
