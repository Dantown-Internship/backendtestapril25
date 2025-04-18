<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:companies,id']
        ];
        $validator = Validator::make($request->all(), $rules, [
            'name.required' => 'Please specify the company name',
            'name.max' => 'The company name cannot exceed 255 characters',
            'email.required' => 'Please specify the company email',
            'email.max' => 'The company email cannot exceed 255 characters',
            'email.email' => 'Invalid email address',
            'email.unique' => 'The email address has been taken'
        ]);

        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        $company = Company::create(['name' => $request->name,'email' => $request->email]);

        return new CompanyResource($company);

    }
}
