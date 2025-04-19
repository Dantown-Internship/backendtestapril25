<?php

namespace App\Http\Controllers;

use App\Data\ExpenseData;
use App\Http\Requests\CreateExpenseRequest;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->company_id;

        $expenses = Expense::query()
            ->forCompany($companyId)
            ->with('user:id,name,email') // Eager loading to avoid N+1
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->paginate(10);

        return new JsonResponse(
            [
                'message' => 'Expenses retrieved successfully',
                'data' => $expenses,
            ],
            Response::HTTP_OK
        );
    }

    public function store(CreateExpenseRequest $request): JsonResponse
    {
        $expenseData = ExpenseData::fromRequest($request->validated());
        dd($expenseData->toArray()); // Debugging line, remove in production
        $expense = Expense::create($expenseData->toArray());

        return new JsonResponse(
            [
                'message' => 'Expense created successfully',
                'data' => $expense,
            ],
            Response::HTTP_CREATED
        );
    }

    public function show(Expense $expense): JsonResponse
    {
        return new JsonResponse(
            [
                'message' => 'Expense retrieved successfully',
                'data' => $expense->load('user:id,name,email'), // Eager loading to avoid N+1
            ],
            Response::HTTP_OK
        );
    }

    public function update(CreateExpenseRequest $request, Expense $expense): JsonResponse
    {
        DB::transaction(function () use ($expense, $request){
            $user = auth()->user();
            $oldValues = $expense->getAttributes();
            $expenseData = ExpenseData::fromRequest($request->validated());
            $expense->update($expenseData->toArray());

            AuditLog::create([
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'action' => 'update_expense',
                'changes' => [
                    'old' => $oldValues,
                    'new' => $expense->getAttributes(),
                ],
            ]);
        });

        return new JsonResponse(
            [
                'message' => 'Expense updated successfully',
                'data' => $expense,
            ],
            Response::HTTP_OK
        );
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $user = auth()->user();
        if (!$user->isAdmin) {
            return new JsonResponse(
                [
                    'message' => 'Unauthorized',
                ],
                Response::HTTP_FORBIDDEN
            );
        }

        $oldValues = $expense->getAttributes();
        $expense->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'delete_expense',
            'changes' => [
                'old' => $oldValues,
                'new' => null,
            ],
        ]);

        return new JsonResponse(
            [
                'message' => 'Expense deleted successfully',
            ],
            Response::HTTP_NO_CONTENT
        );
    }
}
