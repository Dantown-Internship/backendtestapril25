<?php

namespace App\Libs\Actions\Expenses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Http\Resources\ExpenseResource;

class CreateExpenseAction
{
    public function handle($request): ExpenseResource|JsonResponse
    {
        DB::beginTransaction();

        try{
            $expense = Expense::create([
                'company_id' => app('currentCompany')->id,
                'user_id' => auth()->id(),
                'title' => $request->title,
                'category' => $request->category,
                'amount' => $request->amount,
            ]);

            DB::commit();

            return ExpenseResource::make($expense)->additional([
                'message' => 'Expense created successfully',
                'success' => true
            ]);

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