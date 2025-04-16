<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ApiHelpers;
    //
    public function createCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
        if (!$this->isAdmin(auth()->user())) {
            return $this->onError(403, 'You are not authorized to create a company');
        }
        $company = new Company();
        $company->name = $request->name;
        $company->email = $request->email;
        $company->save();

        return response()->json(['message' => 'Company created successfully', 'company' => $company], 201);
    }
}
