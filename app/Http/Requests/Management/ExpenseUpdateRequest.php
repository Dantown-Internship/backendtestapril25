<?php

namespace App\Http\Requests\Management;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'    => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:255'],
            'amount'   => ['sometimes', 'numeric', 'min:0'],
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  => $validator->errors()
        ], 422));
    }
}
