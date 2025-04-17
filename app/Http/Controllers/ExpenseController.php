<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-expenses');

        // get logged in user
        $authUser = auth('api')->user();

        // get search input
        $input = $request->query('keyword');

        // query expenses
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        $cacheKey = "expenses.company.page.{$page}.perPage.{$perPage}.keyword." . ($input ?: 'all');

        $expenses = Cache::tags(['expenses', 'company.' . $authUser->company_id])->remember(
            $cacheKey,
            3600,
            function () use ($authUser, $perPage, $input) {
                return Expenses::where('company_id', $authUser->company_id)->where(function ($query) use ($input) {
                    if ($input) {
                        $query->where('title', 'like', "%$input%")
                            ->orWhere('category', 'like', "%$input%");
                    }
                })->orderBy('created_at', 'desc')->paginate($perPage);
            }
        );

        return response()->json([
            "status" => "success",
            "message" => "Request successfull.",
            "data" => ExpenseResource::collection($expenses)
        ], 200);
    }

    public function store(CreateExpenseRequest $request)
    {
        try {
            // get logged in user
            $authUser = auth('api')->user();

            // validate request
            $request->validated();

            // store expense information
            $expense = new Expenses();
            $expense->company_id = $authUser->company_id; // restricting to logged in users company 
            $expense->user_id = $authUser->id;
            $expense->title = $request->title;
            $expense->amount = $request->amount;
            $expense->category = $request->category;
            $expense->save();

            return response()->json([
                "status" => "success",
                "message" => "Expense created succesfully.",
                "data" => new ExpenseResource($expense)
            ], 201);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(Request $request, string $expId)
    {
        try {
            Gate::authorize('manage-expenses');

            $authUser = auth('api')->user();

            $expense = Expenses::where(function ($query) use ($authUser, $expId) {
                $query->where('id', $expId)->where('company_id', $authUser->company_id);
            })->first();

            if (!$expense) {
                abort(404, "Expense data not found.");
            }

            $expense->update($request->all());

            // flush redis cache
            Cache::tags(['expenses', 'company.' . $authUser->company_id])->flush();

            Log::info("Updating expense with id: {$expId}");

            return response()->json([
                "status" => "success",
                "message" => "Expense updated successfully."
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(string $expId)
    {
        Gate::authorize('delete-expenses');

        $authUser = auth('api')->user();

        $expense = Expenses::find($expId);

        $expense->delete();
        
        // flush redis cache
        Cache::tags(['expenses', 'company.' . $authUser->company_id])->flush();

        Log::info("Deleting expense with id: {$expId}");

        return response()->noContent();
    }
}
