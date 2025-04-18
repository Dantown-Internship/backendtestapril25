<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $companyId = companyID();
            $search = $request->query('search', '');
            $page = $request->query('page', 1);
            $perPage = $request->query('per_page', 10);

            $cacheKey = "expenses_{$companyId}_{$search}_page_{$page}_{$perPage}";

            //  Redis-cached
            $expenses = Cache::tags(['expenses', "company_{$companyId}"])->remember($cacheKey, 60, function () use ($companyId, $search, $perPage) {
                // Company-only expenses
                return Expense::with('user')
                    ->where('company_id', $companyId)
                    ->when($search, function ($query) use ($search) {
                        // Search by title/category
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
                    })
                    // Order by created date desceding 
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
            });
            // Extract and send the relevant data as need
            return $this->respones("Expenses loaded", [
                "expenses" => [
                    "data" => $expenses->items(),
                    "current_page" => $expenses->currentPage(),
                    "per_page" => $expenses->perPage(),
                    "total" => $expenses->total(),
                    "last_page" => $expenses->lastPage(),
                ]
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while loading expenses", null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->respones($this->formatError($validator), null, 400);
            }
            // Only within user's company
            $expense = Expense::create([
                'title' => $request->title,
                'amount' => $request->amount,
                'category' => $request->category,
                'user_id' => userID(),
                'company_id' => companyID(),
            ]);
            // Clear cache to reflect changes
            Cache::tags(['expenses'])->flush();
            return $this->respones("Expense created successfully.", compact("expense"), 201);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while creating expense", null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Int $expenseId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'category' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return $this->respones($this->formatError($validator), null, 400);
            }
            $expense = Expense::where("id", $expenseId)->firstOrFail();
            $old = $expense->toArray();

            $expense->update($request->only(['title', 'amount', 'category']));

            Cache::tags(['expenses'])->flush();

            // Optional: log audit
            audit_log('updated', userID(), companyID(), 'expenses', $old, $expense->toArray());

            return response()->json([
                'message' => 'Expense updated.',
                'data' => $expense,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while updating expense", null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $expenseId)
    {
        try {
            $expense = Expense::where("id", $expenseId)->firstOrFail();

            $old = $expense->toArray();

            $expense->delete();

            Cache::tags(['expenses'])->flush();

            // Optional: log audit
            audit_log('deleted', userID(), companyID(), 'expenses', $old, null);

            return response()->json([
                'message' => 'Expense deleted.',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->respones("An error occurred while updating expense", null, 500);
        }
    }
}