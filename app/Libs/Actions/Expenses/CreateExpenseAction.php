<?php

namespace App\Libs\Actions\Expenses;

use Illuminate\Support\Facades\DB;
use App\Models\Expense;

class CreateExpenseAction
{
    public function handle($request)
    {
        DB::beginTransaction();

        try{

            $expense = Expense::create([
                'company_id' => $request->currentCompany->id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'category' => $request->category,
                'amount' => $request->amount,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Expense created successfully',
                'data' => $expense,
                'success' => true
            ], 201);

        }catch(\Exception $e){

            DB::rollback();

            return response()->json([
                'message' => 'Error creating expense',
                'error' => $e->getMessage(),
                'success' => false
            ], 500);
            
        }
    }
}