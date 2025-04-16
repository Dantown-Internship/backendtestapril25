<?php



namespace App\Services;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\ExpenseCollection;

class ExpenseService
{
    use HttpResponses;

    protected $auditService;

    public function __construct(AuditLogService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function index(Request $request)
    {
        $query = Expense::where('company_id', Auth::user()->company_id);
        if ($request->title) {
            $query->where('title', 'like', "%{$request->title}%");
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $expenses = $query->paginate(10);
        if ($expenses->isEmpty()) {
            $message = 'No expenses found';

            if ($request->title && $request->category) {
                $message = "No expenses found with title '{$request->title}' and category '{$request->category}'";
            } elseif ($request->title) {
                $message = "No expenses found with title '{$request->title}'";
            } elseif ($request->category) {
                $message = "No expenses found in category '{$request->category}'";
            }

            return $this->error($message, 404, '04');
        }

        return $this->success(new ExpenseCollection($expenses), 'Expenses retrieved successfully');
    }





    public function store($data)
    {
        $user = Auth::user();

        $expense = $user->expenses()->create([
            'company_id' => $user->company_id,
            'title'      => $data['title'],
            'amount'     => $data['amount'],
            'category'   => $data['category'],
        ]);

        return $this->success(new ExpenseResource($expense), 'Expense created successfully');
    }


    public function update($data, $id)
    {

        $expense = Expense::find($id);

        if (!$expense) {
            return $this->error('Expense not found', 404, '04');
        }
        if (!in_array(Auth::user()->role, ['Admin', 'Manager'])) {
            $this->error('Unauthorized', 403, '03');
        }
        if ($expense->company_id !== Auth::user()->company_id) {
            $this->error('Unauthorized', 403, '03');
        }


        $oldData = $expense->toArray();

        $expense->update($data);

        $newData = $expense->fresh()->toArray();

        $this->auditService->log('update_expense', $oldData, $newData);
        return $this->success(new ExpenseResource($expense), 'Expense updated successfully');
    }

    public function destroy($id)
    {
        $expense = Expense::find($id);
        if (!$expense) {
            return $this->error('Expense not found', 404, '04');
        }

        if (Auth::user()->role !== 'Admin' || $expense->company_id !== Auth::user()->company_id) {
            $this->error('Unauthorized', 403, '03');
        }

        $oldData = $expense->toArray();

        $expense->delete();
        $this->auditService->log('delete', $oldData, null);

        return $this->success('Expense deleted successfully', 200);
    }
}
