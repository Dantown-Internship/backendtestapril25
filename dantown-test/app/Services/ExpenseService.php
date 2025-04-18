<?php

namespace App\Services;

use Auth;
use Illuminate\Http\Request;

class ExpenseService
{
    public function showAllExpenses(Request $request)
    {
        
        $companyId = Auth::user()->company_id;
        $query = Expense::where('company_id', $companyId)->orderBy('created_at', 'desc');

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->has('category')) {
            $query->where('category', 'like', '%' . $request->input('category') . '%');
        }

        $perPage = $request->input('per_page', 10);

        $expenses = $query->paginate($perPage)->withQueryString();

        return ExpenseResource::collection($expenses);
    }

    public function showAllExpenses2(User $user, string $search_term)
    {
        $companyId = Auth::user()->company_id;
        $query = Expense::where('company_id', $companyId)->orderBy('created_at', 'desc');
        if ($user->role === 'Admin' || $user->role === 'Manager') {
            $query->where('company_id', $user->company_id);
        } else {
            $query->where('user_id', $user->id);
        }
         $query->where(function($mainQuery) use ($search_term){
            $mainQuery->where('title', 'like', "%$search_term%")
            ->orWhere('category', 'like', "%$search_term%");
        });
        return ['success' => true, 'message' => 'fetch data successfully', 'data' => $query->paginate(10)];
    
    }

    public function getExpenseById(User $user, int $id): ?Expense
    {
        $query = Expense::query();
        
        if ($user->role === 'Admin' || $user->role === 'Manager') {
            $query->where('company_id', $user->company_id);
        } else {
            $query->where('user_id', $user->id);
        }
        
        $data = $query->with(['user:id,name', 'company:id,name'])->find($id);
        return ['success' => true, 'message' => 'fetch data successfully', 'data' => $data];
    }

    public function createExpense(User $user, array $data): Expense
    {
        // Ensure company_id is set to the user's company
        $data['company_id'] = $user->company_id;
        
        // Set user_id to the authenticated user unless an admin/manager is creating for someone else
        if (!isset($data['user_id']) || $user->role === 'Employee') {
            $data['user_id'] = $user->id;
        } else {
            // If admin/manager is creating for someone else, ensure they belong to the same company
            if ($user->role === 'Admin' || $user->role === 'Manager') {
                $targetUser = User::find($data['user_id']);
                if (!$targetUser || $targetUser->company_id !== $user->company_id) {
                    throw new Exception('Cannot create expense for user outside your company');
                }
            }
        }
        
        $expense = Expense::create($data);
        return ['success' => true, 'message' => 'data created successfully', 'data' => $expense];
    }

    /**
     * Update an existing expense
     *
     * @param User $user
     * @param int $id
     * @param array $data
     * @return Expense|null
     */
    public function updateExpense(User $user, int $id, array $data): ?Expense
    {
        $oldExpense = $this->getExpenseById($user, $id);
        
        if (!$oldExpense) {
            return null;
        }
        
        // Additional security checks
        // Ensure regular employees can only update their own expenses
        if ($user->role === 'Employee' && $expense->user_id !== $user->id) {
            throw new Exception('Unauthorized to update this expense');
        }
        
        // Ensure company_id cannot be changed
        if (isset($data['company_id'])) {
            unset($data['company_id']);
        }
        
        // Only admin/manager can change the user_id, and only within their company
        if (isset($data['user_id']) && $data['user_id'] !== $expense->user_id) {
            if ($user->role === 'Employee') {
                unset($data['user_id']);
            } else {
                $targetUser = User::find($data['user_id']);
                if (!$targetUser || $targetUser->company_id !== $user->company_id) {
                    throw new Exception('Cannot assign expense to user outside your company');
                }
            }
        }
        
        $expense->update($data);

        // Log the changes
        AuditLog::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'action' => 'update',
            'changes' => json_encode(['old' => $oldExpense, 'new' => $expense])
        ]);
        $data = $expense->fresh();
        return ['success' => true, 'message' => 'data created successfully', 'data' => $data];
    }

    public function deleteExpense(User $user, int $id): bool
    {
        $expense = $this->getExpenseById($user, $id);
        
        if (!$expense) {
            return false;
        }
        
        // Additional security check for employees
        if ($user->role === 'Employee' && $expense->user_id !== $user->id) {
            throw new Exception('Unauthorized to delete this expense');
        }
        
        try {
            return $expense->delete();
        } catch (Exception $e) {
            Log::error('Failed to delete expense: ' . $e->getMessage());
            return false;
        }
    }
        
        
    
    // send notification email to Admin for approval
}