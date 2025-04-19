<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class AdminRegRequest extends BaseFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'company_name' => 'required|string',
            'role' => 'required|in:Admin,Manager,Employee'
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'The email already in use.',
        ];
    }
}
