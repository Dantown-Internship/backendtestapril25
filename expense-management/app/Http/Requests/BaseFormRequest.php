<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;


class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    protected function failedValidation(Validator $validator): void
    {
        $response = Controller::respond(
            'Validation errors', 
            $validator->errors()->toArray(), 
            Response::HTTP_BAD_REQUEST
        );

        throw new HttpResponseException($response);
    }
}
