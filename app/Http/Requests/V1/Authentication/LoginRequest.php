<?php

namespace App\Http\Requests\V1\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'between:1,200',],
            'password' => ['required', 'string', 'between:8,20']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The user email is required.',
            'email.string' => 'The user email must be a valid string.',
            'email.between' => 'The user email must be between 1 and 200 characters.',

            'password.required' => 'The password is required.',
            'password.string' => 'The password must be a string.',
            'password.between' => 'The password must be between 8 and 20 characters.',
        ];
    }
}
