<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

/**
 * @bodyParam name string required The user's full name. Max 255 characters. Example: John Doe
 * @bodyParam email string required The user's email address. Max 255 characters. Must be unique. Example: john@example.com
 * @bodyParam password string required Must be at least 8 characters and confirmed. Example: secret123
 * @bodyParam password_confirmation string required Must be at least 8 characters and confirmed. Example: secret123
 * @bodyParam company.name string required Company name, at least 8 characters. Example: Acme Corporation
 * @bodyParam company.email string required Company contact email address. Example: contact@acme.com
 */

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return
            [
                'name' => ['required',  'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'company.name' => ['required', 'string', 'min:8'],
                'company.email' => ['required', 'string', 'email']
            ];
    }
}
