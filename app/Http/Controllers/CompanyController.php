<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin');
    }

    public function index()
    {
        return response()->json(Company::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $company = Company::create($validated);

        return response()->json(['message' => 'Company created successfully', 'company' => $company]);
    }

    public function show($id)
    {
        $company = Company::find($id);
        return $company ? response()->json($company) : response()->json(['message' => 'Company not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $company = Company::find($id);
        if (!$company) return response()->json(['message' => 'Company not found'], 404);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $company->update($validated);
        return response()->json(['message' => 'Company updated', 'company' => $company]);
    }

    public function destroy($id)
    {
        $company = Company::find($id);
        if (!$company) return response()->json(['message' => 'Company not found'], 404);

        $company->delete();
        return response()->json(['message' => 'Company deleted']);
    }
}
