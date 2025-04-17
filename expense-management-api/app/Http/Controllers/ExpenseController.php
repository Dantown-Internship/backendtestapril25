<?php
namespace App\Http\Controllers;

use App\Jobs\SendWeeklyExpenseReport;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userCompany = $request->authenticated_user->company_id;
        $name        = $request->query('query');
        $cacheKey    = $name ? "expense_search_" . strtolower($name) : "expense_all";

        $expenses = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($userCompany, $name) {
            $query = Expense::where('company_id', $userCompany);

            if ($name) {
                $query->where(function ($q) use ($name) {
                    $q->where('title', 'ILIKE', "%".$name."%")
                        ->orWhere('category', 'ILIKE', "%".$name."%");
                });
            }

            return $query->paginate(10);
        });

        return response()->json([
            "status"  => true,
            "message" => "Expenses retrieved successfully",
            "data"    => $expenses,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => 'required|numeric',
            'title'    => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status"  => false,
                "message" => "Validation error",
                "errors"  => $validator->errors(),
            ], 422);
        }
        $validatedData               = $validator->validated();
        $validatedData['user_id']    = $request->authenticated_user->id;
        $validatedData['company_id'] = $request->authenticated_user->company_id;
        $expense                     = Expense::create($validatedData);
        Cache::forget("expense_all");
        return response()->json([
            "status"  => true,
            "message" => "Expense successfully created",
            "data"    => $expense,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expense = Cache::remember("expense_" . $id, now()->addMinutes(10), function () use ($id) {
            return Expense::find($id);
        });

        if (! $expense) {
            return response()->json([
                "status"  => false,
                "message" => "Expense not found",
            ], 404);
        }

        return response()->json([
            "status"  => true,
            "message" => "Expense retrieved successfully",
            "data"    => $expense,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount'   => 'sometimes|required|numeric',
            'title'    => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status"  => false,
                "message" => "Validation error",
                "errors"  => $validator->errors(),
            ], 422);
        }
        $expense = Expense::find($id);
        if (! $expense) {
            return response()->json([
                "status"  => false,
                "message" => "Expense not found",
            ], 404);
        }
        $originalValues = $expense->getOriginal();
        $validatedData = $validator->validated();
        $expense->update($validatedData);

         $expense->refresh();

          $changedValues = $expense->getChanges();

        Cache::forget("expense_" . $id);
        Cache::forget("expense_all");
        Cache::forget("expense_search_" . strtolower($validatedData['title'] ?? ''));
        Cache::forget("expense_search_" . strtolower($validatedData['category'] ?? ''));

        AuditLog::create([
            'user_id'    => $request->authenticated_user->id,
            'company_id' => $request->authenticated_user->company_id,
            'action'     => 'update',
            'changes'    => json_encode([
                'old' => $originalValues,
                'new' => $changedValues,
            ]),
        ]);

        return response()->json([
            "status"  => true,
            "message" => "Expense successfully updated",
            "data"    => $expense,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::find($id);
        if (! $expense) {
            return response()->json([
                "status"  => false,
                "message" => "Expense not found",
            ], 404);
        }
        $expense->delete();

        Cache::forget("expense_" . $id);
        Cache::forget("expense_all");
        Cache::forget("expense_search_" . strtolower($expense->title));
        Cache::forget("expense_search_" . strtolower($expense->category));

        AuditLog::create([
            'user_id'    => request()->authenticated_user->id,
            'company_id' => request()->authenticated_user->company_id,
            'action'     => 'delete',
            'changes'    => json_encode([
                'old' => $expense->getOriginal(),
                'new' => null,
            ]),
        ]);
        
        return response()->json([
            "status"  => true,
            "message" => "Expense successfully deleted",
        ], 200);
    }
}
