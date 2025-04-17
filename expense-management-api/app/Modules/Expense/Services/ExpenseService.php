<?php

namespace App\Modules\Expense\Services;

use App\Models\AuditLog;
use App\Models\Expense;
use App\Modules\Expense\Dtos\ExpenseDto;
use App\Modules\Expense\Resource\ExpenseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ExpenseService
{
    public function create(ExpenseDto $dto)
    {
        try {
            $expenseData = (array) $dto;

            $result = Expense::create($expenseData);
            return [
                'status' => true,
                'message' => 'Expense created successfully!',
                'data' => new ExpenseService($result)
            ];
        } catch (Throwable $th) {
            logger('Error while creating expense', [$th]);
            return null;
        }
    }

    public function list(string $companyId, int $perPage, string $searchQuery, string $categoryFilter, Request $request)
    {
        try {
            // Create a cache key based on query parameters
            $cacheKey = "expenses:{$companyId}:{$perPage}:{$searchQuery}:{$categoryFilter}:" . $request->query('page', 1);

            // Cache the query result for 5 minutes
            return Cache::remember($cacheKey, 600, function () use ($companyId, $perPage, $searchQuery, $categoryFilter) {
                $query = Expense::with('user')
                    ->where('company_id', $companyId);

                // Apply search filter if provided
                if ($searchQuery) {
                    $query->where(function ($q) use ($searchQuery) {
                        $q->where('title', 'like', "%{$searchQuery}%")
                            ->orWhere('category', 'like', "%{$searchQuery}%");
                    });
                }

                // Apply category filter if provided
                if ($categoryFilter) {
                    $query->where('category', $categoryFilter);
                }

                $expenses = $query->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                return [
                    'status' => true,
                    'message' => 'Expense successfully retrieved',
                    'data' => ExpenseResource::collection($expenses)
                ];
            });
        } catch (Throwable $th) {
            logger('Error while retrieving expenses', [$th]);
            return null;
        }
    }

    public function update(array $validatedData, object $expense, $request)
    {
        try {

            //store old expense for audit log
            $oldValues = $expense->toArray();

            $expense->update($validatedData);

            // Create audit log
            AuditLog::create([
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
                'action' => 'update',
                'changes' => [
                    'old' => $oldValues,
                    'new' => $expense,
                ],
            ]);

            return [
                'status' => true,
                'message' => 'Expense updated successfully!',
                'data' => new ExpenseResource($expense),
            ];
        } catch (Throwable $th) {
            logger('Error while updating expense', [$th]);

            return null;
        }
    }

    public function delete(Expense $expense, Request $request)
    {
        // Store values for audit log 
        $deletedValues = $expense->toArray();

        $expense->delete();

        // Create audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'action' => 'delete',
            'changes' => [
                'old' => $deletedValues,
                'new' => null,
            ],
        ]);

         return [
            'status' => true,
            'message'=> 'Expense Deleted Successfully',
         ];
    }
}
