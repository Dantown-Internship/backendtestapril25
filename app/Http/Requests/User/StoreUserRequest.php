<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Enum;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->uncompromised()],
            'role' => ['required', new Enum(UserRole::class)],
        ];
    }
}
