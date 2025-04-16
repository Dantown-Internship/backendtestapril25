<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cacheKey = 'expenses_' . $request->user()->company_id . '_' . md5($request->fullUrl());
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($request) {
            $query = Expense::with('user')
                ->where('company_id', $request->user()->company_id);

            // Search filters
            if ($request->has('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            return $query->paginate(15);
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string'
        ]);

        $expense = $request->user()->expenses()->create([
            'company_id' => $request->user()->company_id,
            ...$validated
        ]);

        return response()->json($expense, 201);
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
        if (Gate::denies('update-expense', $expense)) {
            return response()->json(['message' => 'You cannot perform this action'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string'
        ]);

        // Audit log before update
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'update',
            'changes' => [
                'old' => $expense->toArray(),
                'new' => $validated
            ]
        ]);

        $expense->update($validated);

        return response()->json($expense);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Expense $expense)
    {
        if (Gate::denies('delete-expense', $expense)) {
            return response()->json(['message' => 'You cannot perform this action'], 403);
        }

        // Audit log before delete
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'delete',
            'changes' => [
                'old' => $expense->toArray()
            ]
        ]);

        $expense->delete();

        return response()->noContent();
    }
}
