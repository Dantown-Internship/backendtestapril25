<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userCompany = $request->authenticated_user->company_id;

        if ($request->has('query')) {
            $query    = $request->input('query');
            $expenses = Expense::where('title', 'LIKE', "%query%")
                ->orWhere('category', 'LIKE', "%$query%")
                ->where('company_id', $userCompany)
                ->paginate(10);
        } else {
            $expenses = Expense::where('company_id', $userCompany)->paginate(10);
        }
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
        $expense = Expense::find($id);

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
        $validatedData = $validator->validated();
        $expense->update($validatedData);
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
        return response()->json([
            "status"  => true,
            "message" => "Expense successfully deleted",
        ], 200);
    }
}
