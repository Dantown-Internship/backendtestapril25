<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumRule;

/**
 * @bodyParam name string required The user's full name. Max 255 characters. Example: Jane Doe
 * @bodyParam email string required The user's email address. Must be unique and valid. Max 255 characters. Example: jane@example.com
 * @bodyParam role string optional The user role. Must be one of: manager, employee, admin. Example: manager
 */
class StoreUserRequest extends FormRequest
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
                "role" => ["sometimes", new EnumRule(RoleEnum::class)],
            ];
    }
}
