<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Expense::where('company_id', $user->company_id)
        ->with('user');

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                ->orWhere('category', 'like', "%$search%");
            });
        }

        /* Using title and category request query differently
        if ($request->has('title')) {
            $query->where('title', 'like', "%{$request->title}%");
        }
        if ($request->has('category')) {
            $query->where('category', 'like', "%{$request->category}%");
        }
        */

        return Cache::remember("expenses_{$request->user()->company_id}", 60, fn () => $query->paginate(10));

    }

    public function store(StoreExpenseRequest $request)
    {
        $user = Auth::user();

        $data = $request->validated();

        $expense = new Expense;
        $expense->title = $data['title'];
        $expense->amount = $data['amount'];
        $expense->company_id = $user->company_id;
        $expense->user_id = $user->id;
        $expense->category = $data['category'];
        $expense->save();

        return new ExpenseResource($expense);
    }

    public function update(UpdateExpenseRequest $request, $id)
    {
        $expense = Expense::find($id);
        $user = Auth::user();

        // Check if the expense exists
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Check if the authenticated user is a member of the company
        if ($expense->company_id !== $user->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Old value before update
        $oldValues = $expense->toArray();

        // Update the expense
        $expense->update($request->validated());

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'action' => 'update',
            'changes' => [
                'before' => $oldValues,
                'after' => $expense->toArray()
            ],
        ]);

        return new ExpenseResource($expense);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $expense = Expense::where('company_id', $user->company_id)->find($id);

        // Check if the expense exists
        if (!$expense) {
            return response()->json(['message' => 'Expense not found'], 404);
        }

        // Check if the authenticated user is a member of the company
        if ($expense->company_id !== $user->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Old Value before delete
        $oldValues = $expense->toArray();

        // Expense Delete
        $expense->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->company_id,
            'action' => 'delete',
            'changes' => [
                'before' => $oldValues,
                'after' => null
            ],
        ]);

        return response()->json(null, 204);
    }


}
