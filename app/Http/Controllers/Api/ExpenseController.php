<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $companyId = auth()->user()->company_id;
        $page = request('page', 1);
        $search = request('search', '');
        $cacheKey = "expenses.page.{$page}.search.{$search}.company.{$companyId}";

        $expenses = Cache::remember($cacheKey, now()->addHours(24), function () use ($search) {
            $expenses = Expense::authCompany();

            $expenses->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
            });

            return $expenses->with('user')->latest()->paginate(10);
        });

        $tagKey = "expenses.company.{$companyId}";

        $keys = Cache::get($tagKey, []);
        $keys[] = $cacheKey;

        Cache::put($tagKey, array_unique($keys), now()->addHours(24));

        return ExpenseResource::collection($expenses);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $expense = Expense::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $validated['category'],
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        return $this->success(new ExpenseResource($expense), 'Expense created successfully', 201);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'title' => ['string', 'max:255'],
            'amount' => ['numeric', 'min:0'],
            'category' => ['sometimes', 'string', 'max:100'],
        ]);

        $expense->update($validated);

        return $this->success(new ExpenseResource($expense), 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return $this->success(null, 'Expense deleted successfully');
    }
}
