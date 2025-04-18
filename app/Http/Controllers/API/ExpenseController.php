<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\ListExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ExpenseController extends Controller
{
    use ResponseTrait;

    public function index(ListExpenseRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $expenses = Cache::remember('expenses', 60, function () use ($requestData) {
                return Expense::query()
                    ->with(['user', 'company'])
                    ->where('company_id', $requestData['company_id'])
                    ->when(! empty($requestData['title']), fn(Builder $query) => $query->where('title', 'like', "%{$requestData['title']}%"))
                    ->when(! empty($requestData['category']), fn(Builder $query) => $query->where('category', 'like',  "%{$requestData['category']}%"))
                    ->paginate(20);
            });

            return $this->successResponse('Company expenses retrieved successfully', ['expenses' => $expenses]);
        } catch (Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }

    public function store(CreateExpenseRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();

            $user = Auth::user();

            $expense = Expense::create(array_merge(
                $requestData,
                ['company_id' => $user->company_id, 'user_id' => $user->id]
            ));

            if (! $expense) {
                return $this->badRequestResponse('Could not create expense');
            }

            return $this->successResponse('Expense created successfully', ['expense' => $expense->load(['user', 'company'])], 201);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }

    public function update(int $id, UpdateExpenseRequest $request): JsonResponse
    {
        try {
            $expense = Expense::find($id);

            if (! $expense) {
                return $this->notFoundResponse('Could not retrieve expense');
            }

            $expense->update(array_filter($request->validated()));

            return $this->successResponse('Expense updated successfully', ['expense' => $expense->load(['user', 'company'])->fresh()]);
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $expense = Expense::find($id);

            if (! $expense) {
                return $this->notFoundResponse('Could not retrieve expense');
            }

            $expense->delete();

            return $this->successResponse('Expense deleted successfully');
        } catch (\Throwable $throwable) {
            return $this->serverErrorResponse(throwable: $throwable, message: 'Server error');
        }
    }
}
