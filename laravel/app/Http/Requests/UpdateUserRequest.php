<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'      => 'sometimes|required|string|max:255',
            'email'     => 'sometimes|required|email|unique:users,email,' . $this->user_id,
            'password'  => 'sometimes|required|string|min:6',
            'role'      => 'sometimes|required|in:Admin,Manager,Employee',
        ];
    }
}
