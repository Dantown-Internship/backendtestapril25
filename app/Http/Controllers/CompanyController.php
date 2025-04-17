<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        print_r($request);
        try {
            // Check if the authenticated user is an Admin
            $user = Auth::user();
            print_r($user);
            if (!$user || $user->role !== 'Admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validate request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:companies,email',
            ]);

            // Create company
            $company = Company::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return response()->json([
                'message' => 'Company created successfully.',
                'company' => $company
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
