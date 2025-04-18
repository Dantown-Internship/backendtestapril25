<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseEditRequest;
use App\Http\Requests\ExpensePostRequest;
use App\Models\AuditLog;
use App\Models\Expense;
use App\utility\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Util::Auth();

        $cacheKey = "expenses_{$user->company_id}_page_" . $request->get('page', 1) . '_search_' . $request->get('search');
        $expenses = Cache::remember($cacheKey, 60, function () use ($user, $request) {
            $query = Expense::with('users')->where('company_id', $user->company_id);
            if ($search = $request->query('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }
            return $query->paginate(10);
        });
        return response()->json($expenses);
    }


    public function create(ExpensePostRequest $request)
    {
        $this->authorize('create', Expense::class);
        try {
            $user = Util::Auth();
            //Admin Create A New Expense
            $expense = Expense::createExpenseRecord($request, $user->id, $user->company_id);
            // clear first pegination
            Cache::forget("expenses_{$user->company_id}_page_1_search_");
            return response()->json(['sucess' => true, 'message' => 'Expense Added Successfully', 'expense' => $expense]);
        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }



    public function update(ExpenseEditRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        $oldData = $expense->toArray();

        try {
            $expense->title = $request->title ?? $expense->title;
            $expense->category = $request->category ?? $expense->category;
            $expense->amount = $request->amount ?? $expense->amount;
            $expense->update();

            AuditLog::logAudit('updated', $oldData, $expense);

            // clear first pegination
            Cache::forget("expenses_{$expense->company_id}_page_1_search_");
            return response()->json(['success' => true, 'message' => 'Expense Updated Successfully', 'expense' => $expense]);
        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }

    public function delete(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $oldData = $expense->toArray();

        try {
            $expense->delete();
            // clear first pegination
            Cache::forget("expenses_{$expense->company_id}_page_1_search_");
            AuditLog::logAudit('deleted', $oldData);
  
            return response()->json(['success' => true, 'message' => 'Expense Deleted Successfully']);
        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }
}
