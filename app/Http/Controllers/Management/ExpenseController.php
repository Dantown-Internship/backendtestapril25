<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Services\Management\ExpenseService;
use App\Http\Requests\Management\ExpenseRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Auth\RoleService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\ExpenseResource;
use App\Http\Requests\Management\ExpenseUpdateRequest;

class ExpenseController extends Controller
{

    public function __construct(
        protected ExpenseService $expenseService,
        protected RoleService $roleService
    ) {}


    public function expenses(Request $request): JsonResponse
    {
        $filters = $request->only(['title', 'company']);

        $expenses = $this->expenseService->expenses($filters);

        if ($expenses->isEmpty()) {
            return dantownResponse(null, 404, "No record found");
        }

        $responseData = [
            'expenses' => ExpenseResource::collection($expenses),
            'meta' => [
                'current_page' => $expenses->currentPage(),
                'last_page'    => $expenses->lastPage(),
                'total'       => $expenses->total(),
            ]
        ];

        return dantownResponse($responseData, 200, "Retrieved successfully!");
    }




    public function create(ExpenseRequest $request): JsonResponse
    {

        $expenseData = array_merge($request->validated(), [
            'user_id'    => auth()->user()->id,
            'company_id' => auth()->user()->company_id,
        ]);

        $expense = $this->expenseService->create($expenseData);
        return dantownResponse($expense, 201, "Expense created!", true);
    }


    public function delete(string $expenseId)
    {
        if (empty($expenseId)) {
            return dantownResponse([], 400, 'You need an expense ID!', false);
        }

        try { 
            authorizeRole('admin');
            
            $user = $this->expenseService->delete($expenseId);
            return dantownResponse($user, 200, 'Resource deleted!', true);
        } catch (ModelNotFoundException $e) {
            return dantownResponse([], 404, "No record found!", false);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        }
    }


    public function update(ExpenseUpdateRequest $request, string $expenseId): JsonResponse
    {
        if (empty($expenseId)) {
            return dantownResponse([], 400, 'You need an expense ID!', false);
        }
    
        try {
            authorizeRole(['admin', 'manager']);
    
            $expense = $this->expenseService->update($expenseId, $request->validated());
    
            if (!$expense) {
                return dantownResponse([], 404, 'Expense not found.', false);
            }
    
            return dantownResponse(new ExpenseResource($expense), 200, 'Expense update completed!', true);
        } catch (AuthorizationException $e) {
            return dantownResponse([], 403, $e->getMessage(), false);
        }
    }
    
}
