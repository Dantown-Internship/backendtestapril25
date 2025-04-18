<?php

namespace App\Services;

use App\Models\User;
use App\Models\Expense;
use App\Models\AuditLog;
use Auth;
use Illuminate\Http\Request;

class ExpenseService
{
    public function showAllExpenses(Request $request): array
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

        return ['success' => true, 'message' => 'fetch data successfully', 'data' => $expenses];
    }

    public function showAllExpenses2(User $user, string $search_term): array
    {
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

    public function getExpenseById(User $user, int $id): ? array
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

    public function createExpenses(User $user, array $data): array
    {
        $data['company_id'] = $user->company_id;
        $data['user_id'] = $user->id;
        
        $expense = Expense::create($data);
         AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'create Expense',
            'changes' => $expense->title
        ]);
        return ['success' => true, 'message' => 'data created successfully', 'data' => $expense];
    }


    public function updateExpense(User $user, int $id, array $data): ? array
    {
        $oldExpense = $this->getExpenseById($user, $id)['data'];
        
        if (!$oldExpense) {
            return ['success' => false, 'message' => 'No data found', 'data' => []];
        }
        
        if ($user->role === 'Employee') {
            return ['success' => false, 'message' => 'Unauthorized to update this expense', 'data' => []];
        }
        
        $data['company_id'] = $oldExpense->company_id;
        $data['user_id'] = $oldExpense->user_id;
        $updatedExpense = $oldExpense->update($data);
        $updatedExpenseData = $updatedExpense->fresh();

        // Log the changes
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'update',
            'changes' => json_encode(['old' => $oldExpense, 'new' => $updatedExpenseData])
        ]);
        
        return ['success' => $updatedExpense, 'message' => 'data created successfully', 'data' => $updatedExpenseData];
    }

    

    public function deleteExpense(User $user, int $id): array
    {
        $expense = $this->getExpenseById($user, $id)['data'];
        
        if ($user->role === 'Employee') {
            return ['success' => false, 'message' => 'Unauthorized to delete this expense', 'data' => []];
        }
        
        $isExpenseDeleted = $expense->delete();

         // Log the changes
        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => 'delete Expense',
            'changes' => json_encode(['deleted' => $expense])
        ]);
        return ['success' => $isExpenseDeleted, 'message' => 'data deleted successfully', 'data' => []];
    }
        
        
    
    // send notification email to Admin for approval
}