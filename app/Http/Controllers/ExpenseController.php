<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Events\ExpenseDeleted;
use App\Events\ExpenseUpdated;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Cache;
use Mail;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses for the authenticated user's company.
     */
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $cacheKey = 'expenses:company:' . $companyId . ':page:' . $page . ':search:' . md5($search);
        $cacheListKey = 'expenses:company:' . $companyId . ':keys'; // Cache key for the list of keys
        $cacheTtl = now()->addMinutes(5);

        $expenses = Cache::remember($cacheKey, $cacheTtl, function () use ($companyId, $request) {
            $query = Expense::where('company_id', $companyId)
                ->with('user:id,name');

            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                        ->orWhere('category', 'like', '%' . $searchTerm . '%');
                });
            }

            return $query->paginate(20);
        });

        // Store the cache key in the list
        $keyList = Cache::get($cacheListKey, []); // Get the existing list or an empty array
        $keyList[] = $cacheKey; // Add the new key
        Cache::put($cacheListKey, $keyList, $cacheTtl->addMinute()); // Store the updated list

        // Mail::to("nsikanabasi.idung@gmail.com")->send(new \App\Mail\WeeklyReportEmail(Company::find($companyId), $expenses["data"]));

        return response()->json($expenses);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string|max:255',
        ]);

        $expense = Expense::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            'title' => $validatedData['title'],
            'amount' => $validatedData['amount'],
            'category' => $validatedData['category'],
        ]);

        $this->clearExpenseCache($request->user()->company_id);

        return response()->json($expense, 201);
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        if ($expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized to update this expense'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0.01',
            'category' => 'sometimes|string|max:255',
        ]);

        $originalAttributes = $expense->getAttributes();
        $expense->fill($validatedData);
        $dirty = $expense->getDirty();
        $expense->save();

        event(new ExpenseUpdated($expense, $originalAttributes, $dirty));

        $this->clearExpenseCache($request->user()->company_id);

        return response()->json($expense);
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->company_id !== $request->user()->company_id) {
            return response()->json(['message' => 'Unauthorized to delete this expense'], 403);
        }

        event(new ExpenseDeleted($expense));

        $expense->delete();

        $this->clearExpenseCache($request->user()->company_id);

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    /**
     * Clear the expense cache for a specific company.
     */
    private function clearExpenseCache($companyId)
    {
        $cacheListKey = 'expenses:company:' . $companyId . ':keys';
        $keys = Cache::get($cacheListKey, []); // Get the list of keys from the cache
        Cache::forget($cacheListKey); // Clear the list

        foreach ($keys as $key) {
            Cache::forget($key); // Delete each key from the cache
        }
    }
}