<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\AuditLog;



class ExpenseController extends Controller
{

    /**
 * Get a list of expenses
 *
 * @group Expenses
 * @authenticated
 * @queryParam title Filter by title (optional)
 * @queryParam category Filter by category (optional)
 *
 * @response 200 {
 *   "data": [
 *     {
 *       "id": 1,
 *       "title": "Office rent",
 *       "amount": "1000.00",
 *       "category": "Operations"
 *     }
 *   ]
 * }
 */

public function index(Request $request)

{
    $user = $request->user();
    $search = $request->input('search');
    $page = $request->input('page', 1);

    $cacheKey = "expenses_{$user->company_id}_page{$page}_search_" . md5($search);

    $expenses = cache()->remember($cacheKey, 60, function () use ($user, $search) {
        $query = Expense::with('user')
            ->where('company_id', $user->company_id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    });

    return response()->json($expenses);
}




public function store(Request $request)
{
    $user = $request->user();

    $data = $request->validate([
        'title' => 'required|string',
        'amount' => 'required|numeric',
        'category' => 'required|string',
    ]);

    $expense = Expense::create([
        'company_id' => $user->company_id,
        'user_id' => $user->id,
        'title' => $data['title'],
        'amount' => $data['amount'],
        'category' => $data['category'],
    ]);

    return response()->json($expense, 201);
}


public function update(Request $request, $id)
{
    $user = $request->user();

    $expense = Expense::where('company_id', $user->company_id)->findOrFail($id);

    $validated = $request->validate([
        'title' => 'string|max:255',
        'amount' => 'numeric',
        'category' => 'string'
    ]);

    $expense->update($validated);


    $expens = Expense::findOrFail($id);

    // Clone the old data before updating
    $oldExpense = $expens->replicate();

    // Update with validated data (you may add validation here)
    $expens->update($request->all());

    // Create audit log entry
    AuditLog::create([
        'user_id' => auth()->user()->id,
        'company_id' => auth()->user()->company_id,
        'action' => 'update',
        'changes' => json_encode([
            'old' => $oldExpense,
            'new' => $expens
        ])
    ]);

    return response()->json($expense);
}


public function destroy(Request $request, $id)
{
    $user = $request->user();

    $expense = Expense::where('company_id', $user->company_id)->findOrFail($id);

    $expense->delete();

    return response()->json(['message' => 'Expense deleted']);
}




// ///////////////////////////////////////////////////////////////////
public function destroyForLog($id)
{
    // Find the expense by its ID
    $expense = Expense::findOrFail($id);

    // Capture the old values before the delete (we log the entire record)
    $oldValues = $expense->getAttributes();

    // Delete the expense
    $expense->delete();

    // Log the delete action in the audit logs
    AuditLog::create([
        'user_id' => auth()->user()->id, // ID of the logged-in user
        'company_id' => auth()->user()->company_id, // Company associated with the logged-in user
        'action' => 'delete', // Action performed (delete)
        'changes' => json_encode([
            'old' => $oldValues, // Old values (before delete)
            'new' => null, // No new values since the record is deleted
        ])
    ]);

    // Return a success message or the deleted record (optional)
    return response()->json(['message' => 'Expense deleted successfully.']);
}



}





