<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $query = Expense::with('user.company')
            ->where('company_id', $request->user()->company_id);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        // Api resource can be use here
        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->only(['title', 'amount', 'category']);
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $expense = Expense::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            ...$data
        ]);

        return $this->successResponse('Expense created successfully.', $expense, 201);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->company_id !== $request->user()->company_id) {
            return $this->errorResponse('Not found', 404);
        }

        $data = $request->only(['title', 'amount', 'category']);
        $validator = Validator::make($data, [
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first());
        }

        $expense->update($data);
        return $expense;
    }

    public function destroy(Request $request, Expense $expense)
    {
        // Verify expense belongs to user's company
        if ($expense->company_id !== $request->user()->company_id) {
            return $this->errorResponse('Not found', 404);
        }

        $expense->delete();
        return $this->successResponse('Expense deleted successfully.');
    }
}
