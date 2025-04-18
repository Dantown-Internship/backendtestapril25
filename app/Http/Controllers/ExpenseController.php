<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //add authorization for all users admin, employee and manager
        $this->authorize('viewAny', Expense::class);

        $companyId = Auth::User()->company_id;
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $category = $request->get('category');

        $cacheKey = "expenses:{$companyId}:page:{$page}:search:{$search}:category:{$category}";

        $cached = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($companyId, $search, $category) {
            $query = Expense::with('user')
                ->where('company_id', $companyId)
                ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                ->when($category, fn($q) => $q->where('category', $category))
                ->latest();

            return $query->paginate(10);
        });

        return response()->json(['message' => 'Fetch successful', 'data' => $cached]);
        
        // $expenses = Expense::with('user')
        //     ->where('company_id', Auth::User()->company_id)
        //     ->when($request->search, function($query) use ($request) {
        //         $query->where('title', 'like', "%{$request->search}%")
        //               ->orWhere('category', 'like', "%{$request->search}%");
        //     })
        //     ->paginate(10);

        // return response()->json(['message' => 'Fetch successful', 'data' => $expenses]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric',
            'category' => 'required',
        ]);

        $expense = Expense::create([
            'title' => $data['title'],
            'amount' => $data['amount'],
            'category' => $data['category'],
            'user_id' => Auth::User()->id,
            'company_id' => Auth::User()->company_id,
        ]);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense
        ], 201);
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
        $this->authorize('update', $expense);

        $old = $expense->toArray();
        $expense->fill($request->only(['title', 'amount', 'category']));
        $expense->save();

        AuditLog::create([
            'user_id' => Auth::User()->id,
            'company_id' => Auth::User()->company_id,
            'action' => 'update',
            'changes' => json_encode(['old' => $old, 'new' => $expense->toArray()])
        ]);

        return response()->json(['message' => 'Expense updated successfully', 'data' => $expense]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        AuditLog::create([
            'user_id' => Auth::User()->id,
            'company_id' => Auth::User()->company_id,
            'action' => 'delete',
            'changes' => json_encode(['old' => $expense->toArray(), 'new' => null])
        ]);

        $expense->delete();

        return response()->json(['message' => 'Expense Deleted']);
    }
}
