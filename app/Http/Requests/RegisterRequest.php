<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    use ResponseHelper;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => [new Enum(RoleEnum::class), 'required'],
            'password' => 'required|string|min:8',
            'company_id' => 'uuid|required'
        ];
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(
            $this->forbidden()
        );
    }
}
