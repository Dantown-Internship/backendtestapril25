<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses with pagination and search.
     *
     * @group Expense Management
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);
        
        // Enable query logging to check eager loading
        DB::enableQueryLog();
        
        // Create a unique cache key based on user company and request parameters
        $cacheKey = 'expenses_' . $request->user()->company_id . '_' . http_build_query($request->all());
        
        // Check if we have a cache hit
        $cacheExists = Cache::has($cacheKey);
        if ($cacheExists) {
            Log::info('Cache HIT: ' . $cacheKey);
        } else {
            Log::info('Cache MISS: ' . $cacheKey);
        }
        
        // Cache the results for 15 minutes
        $result = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request, $cacheKey) {
            $query = Expense::query()
                ->where('company_id', $request->user()->company_id);
            
            // If not admin or manager, only show own expenses
            if ($request->user()->role === 'Employee') {
                $query->where('user_id', $request->user()->id);
            }
            
            // Search by title
            if ($request->has('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }
            
            // Filter by category
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            
            // Filter by minimum amount
            if ($request->has('min_amount')) {
                $query->where('amount', '>=', $request->min_amount);
            }
            
            // Filter by maximum amount
            if ($request->has('max_amount')) {
                $query->where('amount', '<=', $request->max_amount);
            }
            
            // Eager load the user and company relationships to prevent N+1 queries
            return $query->with(['user', 'company'])->paginate(15);
        });
        
        // Log the executed queries for debugging
        $queries = DB::getQueryLog();
        Log::info('Expense Eager Loading Check - Query Count: ' . count($queries));
        foreach ($queries as $index => $query) {
            Log::info("Query #{$index}: " . $query['query']);
        }
        
        return $result;
    }

    /**
     * Store a newly created expense in storage.
     *
     * @group Expense Management
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Expense::class);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
        ]);

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'category' => $request->category,
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        // Clear the cache for this company's expenses
        $this->clearExpenseCache($request->user()->company_id);

        return response()->json($expense, 201);
    }

    /**
     * Display the specified expense.
     *
     * @group Expense Management
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Cache individual expense views
        $cacheKey = 'expense_' . $id;
        
        // Check if we have a cache hit
        $cacheExists = Cache::has($cacheKey);
        if ($cacheExists) {
            Log::info('Cache HIT: ' . $cacheKey);
        } else {
            Log::info('Cache MISS: ' . $cacheKey);
        }
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($id) {
            // Eager load the user and company relationships
            $expense = Expense::with(['user', 'company'])->findOrFail($id);
            
            $this->authorize('view', $expense);
            
            return response()->json($expense);
        });
    }

    /**
     * Update the specified expense in storage.
     *
     * @group Expense Management
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        
        $this->authorize('update', $expense);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'category' => 'sometimes|required|string|max:100',
        ]);

        $expense->update($request->only(['title', 'amount', 'category']));
        
        // Clear both the list cache and the individual expense cache
        $this->clearExpenseCache($expense->company_id);
        Cache::forget('expense_' . $id);
        Log::info('Cache INVALIDATION: expense_' . $id);
        
        return response()->json($expense);
    }

    /**
     * Remove the specified expense from storage.
     *
     * @group Expense Management
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        
        $this->authorize('delete', $expense);
        
        // Store company_id before deleting for cache clearing
        $companyId = $expense->company_id;
        
        $expense->delete();
        
        // Clear the cache for this company's expenses
        $this->clearExpenseCache($companyId);
        Cache::forget('expense_' . $id);
        Log::info('Cache INVALIDATION: expense_' . $id);
        
        return response()->json(['message' => 'Expense deleted successfully'], 200);
    }
    
    /**
     * Helper method to clear expense caches for a company
     *
     * @param  int  $companyId
     * @return void
     */
    private function clearExpenseCache($companyId)
    {
        // Use pattern-based cache clearing for all keys starting with 'expenses_{company_id}_'
        // This ensures all paginated and filtered lists are cleared
        Cache::forget('expenses_' . $companyId . '_*');
        Log::info('Cache INVALIDATION: expenses_' . $companyId . '_*');
    }
} 