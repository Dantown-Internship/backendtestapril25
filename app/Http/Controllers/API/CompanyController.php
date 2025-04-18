<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display the authenticated user's company details.
     */
    public function show(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->notFound('Company not found');
        }

        return response()->success('Company details retrieved successfully', ['company' => $company]);
    }

    /**
     * Update the authenticated user's company.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $company = $user->company;

        if (!$company) {
            return response()->notFound('Company not found');
        }

        // Only admin can update company details
        if (!$user->isAdmin()) {
            return response()->unauthorized('Unauthorized. Only admin can update company details.');
        }

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:companies,email,' . $company->id,
        ]);

        $company->update($request->only('name', 'email'));

        return response()->success('Company updated successfully', ['company' => $company]);
    }

    /**
     * Get statistics for the company (for dashboard).
     */
    public function statistics(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return response()->notFound('Company not found');
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

        return response()->success('Company statistics retrieved successfully', [
            'total_expenses' => $totalExpenses,
            'user_count' => $userCount,
            'expense_count' => $expenseCount,
            'recent_expenses' => $recentExpenses,
            'expenses_by_category' => $expensesByCategory,
        ]);
    }
}
