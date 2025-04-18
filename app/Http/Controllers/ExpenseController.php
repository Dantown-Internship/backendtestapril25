<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseEditRequest;
use App\Http\Requests\ExpensePostRequest;
use App\Models\Expense;
use App\utility\Util;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request){
        $user = Util::Auth();
        $query = Expense::with('user')->where('company_id', $user->company_id);
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%search%")
                ->orWhere('category', 'like', "%search%");
            });
        }
        return response()->json($query->paginate(10));
    }


    public function create(ExpensePostRequest $request){
        $this->authorize('create', Expense::class);
        try {
            $user = Util::Auth();
            //Admin Create A New Expense
            $expense = Expense::createExpenseRecord($request, $user->id, $user->company_id);
            return response()->json(['sucess' => true, 'message' => 'Expense Added Successfully', 'expense' => $expense]);

        } catch (\Throwable $th) {
            return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
        }
    }



    public function update(ExpenseEditRequest $request, Expense $expense){
        $this->authorize('update', $expense);

       try {
        $expense->title = $request->title ?? $expense->title;
        $expense->category = $request->category ?? $expense->category;
        $expense->amount = $request->amount ?? $expense->amount;

        $expense->update();

        return response()->json(['success' => true, 'message' => 'Expense Updated Successfully', 'expense' => $expense]);
       } catch (\Throwable $th) {
        return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
       }
    }

    public function delete(Expense $expense){
        $this->authorize('delete', $expense);

       try {
        $expense->delete();

        return response()->json(['success' => true, 'message' => 'Expense Deleted Successfully']);
       } catch (\Throwable $th) {
        return response()->json(['sucess' => false, 'message' => $th->getMessage()]);
       }
    }
}
