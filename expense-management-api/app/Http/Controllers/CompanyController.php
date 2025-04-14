<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();

        return response()->json([
            "status"  => true,
            "message" => "Companies retrieved successfully",
            "data"    => $companies,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => false,
                "message" => "Validation error",
                "errors"  => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $company = Company::create($validatedData);
        return response()->json(["status" => true, "message" => "Company successfully created", "data" => $company], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                "status"  => false,
                "message" => "Company not found",
            ], 404);
        }

        return response()->json([
            "status"  => true,
            "message" => "Company retrieved successfully",
            "data"    => $company,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => false,
                "message" => "Validation error",
                "errors"  => $validator->errors()
            ], 422);
        }

        $company = Company::find($id);
        if (!$company) {
            return response()->json([
                "status"  => false,
                "message" => "Company not found",
            ], 404);
        }

        $company->update($validator->validated());

        return response()->json([
            "status"  => true,
            "message" => "Company updated successfully",
            "data"    => $company,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            "status"  => true,
            "message" => "Company deleted successfully",
        ], 200);
    }
}
