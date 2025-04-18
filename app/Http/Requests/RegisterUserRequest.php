<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Since the request can only be make by the admin only.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role->value === UserRole::Admin->value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|string|in:' . implode(',', array_column(UserRole::cases(), 'value')),
        ];
    }
}
