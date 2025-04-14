<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cacheKey = 'expenses_' . Auth::user()->company_id . '_' . md5(json_encode($request->all()));
    
        return Cache::remember($cacheKey, now()->addMinutes(30), function() use ($request) {
        $query = Expense::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%");
            });
        }

        return $query->paginate(10);
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        $expense = Auth::user()->expenses()->create([
            'company_id' => Auth::user()->company_id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
        ]);

        return response()->json($expense, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string|max:255',
        ]);

        $changes = [];
        foreach ($validated as $key => $value) {
            if ($expense->$key != $value) {
                $changes[$key] = [
                    'old' => $expense->$key,
                    'new' => $value
                ];
            }
        }

        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'action' => 'update_expense',
                'changes' => $changes
            ]);
        }

        $expense->update($validated);

        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        // Convert to JSON string explicitly
        $changes = json_encode([
            'deleted_expense' => $expense->only(['id', 'title', 'amount', 'category']),
            'deleted_at' => now()->toDateTimeString()
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'action' => 'delete_expense',
            'changes' => $changes
        ]);

        $expense->delete();

        return response()->json(null, 204);
    }
}
