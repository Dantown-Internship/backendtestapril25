<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Company;
use App\Policies\ExpensePolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;

class ExpenseController extends Controller
{

  use AuthorizesRequests;

  //Create Expense
    public function create(Request $req) {
     $validated = $req->validate([
        "title"=>"Required",
        "amount"=>"Required",
        "category"=>"Required"
      ]);


     $user = auth()->user();

       $this->authorize('create', Expense::class);
      
     $expense = new Expense($validated);

     $expense->company_id = $user->company_id;
     $expense->user_id = auth()->id();

     $expense->save();

      return response()->json([
        "message" => "Expense Created successfully.",
        "data" => [
            "Expense Detail" => $expense,
        ]
    ], 201);

    }

    //Get Expense
  
      public function view(Request $request)
      {
          $expenses = Expense::with(['company', 'user'])
          ->where('company_id', auth()->user()->company_id)
          ->when($request->search, function ($query, $search) {
            $query->where('category', 'like', "%{$search}%");
           })
          ->paginate($request->per_page ?? 1);
      
          return response()->json($expenses);
      }
      



    //Update Expense
    public function update(Request $req, Expense $expense) {
      
      $this->authorize('update', $expense);

      $expense->title = $req->title;
      $expense->amount = $req->amount;
      $expense->category = $req->category;
      $expense->save();

      return response()->json([
        "message" => "Record Updated successfully.",
      
    ], 201);
    }

    //Delete Expense 
    public function delete(Expense $expense)
    {
     
    try {
        $this->authorize('delete', $expense);
     } catch (AuthorizationException $e) {
        return response()->json([
            'message' => 'You are not authorized to delete this expense.'
        ], 403);
      }

        $expense->delete();

        return response()->json([
          "message" => "Record deleted successfully.",
        
      ], 204);

    }
  }
