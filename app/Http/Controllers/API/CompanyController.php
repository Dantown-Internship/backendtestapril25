<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display the authenticated user's company details.
     */
    public function show(Request $request)
    {
        $company = $request->user()->company;


        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json(['company' => $company]);
    }

    /**
     * Update the authenticated user's company.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $company = $user->company;

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Only admin can update company details
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized. Only admin can update company details.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
        ]);

        $company->update($request->only('name', 'email'));

        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $company
        ]);
    }

    /**
     * Get statistics for the company (for dashboard).
     */
    public function statistics(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Get total expenses, user count, and other relevant metrics
        $totalExpenses = $company->expenses()->sum('amount');
        $userCount = $company->users()->count();
        $expenseCount = $company->expenses()->count();
        $recentExpenses = $company->expenses()->with('user')->latest()->take(5)->get();

        // Get expenses by category (for charts)
        $expensesByCategory = $company->expenses()
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->get();

        return response()->json([
            'total_expenses' => $totalExpenses,
            'user_count' => $userCount,
            'expense_count' => $expenseCount,
            'recent_expenses' => $recentExpenses,
            'expenses_by_category' => $expensesByCategory,
        ]);
    }
}
