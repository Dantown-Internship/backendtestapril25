<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Enum;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', new Enum(UserRole::class)],
        ];
    }
}
