<?php
declare(strict_type=1);
namespace App\Http\Controllers\Api;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExpenseController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        $expenses = Expense::query()
        ->with('company', 'user')
        ->when($request->has('category'), function($query) use ($request) {
            $query->where('category', 'LIKE', '%' . trim($request->input('category')) . '%');
        })
        ->when($request->filled('title'), function ($query) use ($request) {
            $query->where('title', 'LIKE', '%' . trim($request->input('title')) . '%');
        })
        ->whereRelation('company', 'id', auth()->user()->company_id)
        ->orderByDesc('category')
        ->paginate(20);

        return $this->success([
            'expenses' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'title' => 'required',
            'category' => 'string|required'
        ]);
        $data['user_id'] = $request->user()->id;
        $data['company_id'] = $request->user()->company_id;
        $expense = Expense::create($data);
        return $this->success(['expense' => $expense], 'Expense created successfully', Response::HTTP_CREATED);
    }

    public function show(Expense $expense)
    {
        return $this->success([
            'expense' => $expense->load('company')
        ]);
    }


    public function update(Request $request, Expense $expense)
    {
        if(!auth()->user()->can('update', $expense)) {
            return $this->forbidden();
        }

        $data = $request->validate([
            'amount' => ['required', 'regex:/^\d+(\.\d{1,2})?$/'],
            'title' => 'required',
            'category' => 'string|required'
        ]);
       
        $expense->amount = $data['amount'];
        $expense->title = $data['title'];
        $expense->category = $data['category'];
        $expense->save();
        return $this->success([
            'expense' => $expense->load('company')
        ]);
    }


    public function destroy(Expense $expense)
    {
        if(!auth()->user()->can('delete', $expense)) {
            return $this->forbidden();
        }
        $expense->delete();
        return $this->success([], 'Expense deleted successfully');
    }
}
