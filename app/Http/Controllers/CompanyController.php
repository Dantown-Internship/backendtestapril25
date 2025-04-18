<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    //

    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Company::class],
        ]);

        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['company' => $company])->toResponse($request);
    }
}
