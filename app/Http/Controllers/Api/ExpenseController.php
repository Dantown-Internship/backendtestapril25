<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'expenses_' . auth()->user()->company_id . '_' . md5(json_encode($request->all()));

        return Cache::remember($cacheKey, now()->addMinutes(30), function() use ($request) {
            $query = Expense::with('user')
                ->where('company_id', auth()->user()->company_id);

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                      ->orWhere('category', 'like', "%$search%");
                });
            }

            return $query->paginate($request->per_page ?? 15);
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'    => 'required|string|max:255',
                'amount'   => 'required|numeric',
                'category' => 'required|string|max:255',
            ]);

            $expense = Expense::create([
                'title'      => $validated['title'],
                'amount'     => $validated['amount'],
                'category'   => $validated['category'],
                'company_id' => $request->user()->company_id,
                'user_id'    => $request->user()->id
            ]);

            return response()->json($expense, 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        try {
            // Only managers and admins can update expenses
            if (auth()->user()->isEmployee()) {
                abort(403, 'Only managers and admins can update expenses.');
            }

            // Validate incoming request data
            $request->validate([
                'title'    => 'sometimes|string|max:255',
                'amount'   => 'sometimes|numeric|min:0',
                'category' => 'sometimes|string|max:255',
            ]);

            $updatedFields = $request->only(['title', 'amount', 'category']);

            $changes = [
                'old' => $expense->only(array_keys($updatedFields)),
                'new' => $updatedFields,
            ];

           

            AuditLog::create([
                'user_id'   => auth()->id(),
                'company_id'=> auth()->user()->company_id,
                'action'    => 'update',
                'changes'   => json_encode($changes),
            ]);

            // Perform the update
            $expense->update($request->all());

            // Return updated expense
            return response()->json([
                'message' => 'Expense updated successfully.',
                'data'    => $expense
            ], 200);

        } catch (\Throwable $e) {
            // Return error message if something fails
            return response()->json([
                'error'   => 'Failed to update expense.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        // Only admins can delete expenses
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admins can delete expenses.');
        }

        // Log deletion
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'delete',
            'changes' => ['old' => $expense->toArray()],
        ]);

        $expense->delete();

        return response()->json(null, 204);
    }
}
