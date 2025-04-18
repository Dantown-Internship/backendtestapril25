<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Constant;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\UserResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index(Request $request) {
        $user    = $request->user();
        $search  = $request->get('search', '');
        $perPage = $request->get('perPage', 10);
        $page    = $request->get('page', 1);

        $cacheKey = implode(':', [
            'expenses',
            $user->company_id,
            'search', md5($search),
            'page',   $page,
            'perPage',$perPage,
        ]);

        //return Expense::where('company_id', $user->company_id)->paginate(10);

        $expenses = Cache::remember($cacheKey, now()->addMinutes(5), function() use ($user, $request) {
            return Expense::with('user')
                ->where('company_id', $user->company_id)
                ->when($request->search, function($query, $search) {
                    return $query->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                })
                ->orderByDesc('updated_at')
                ->paginate($request->perPage ?? 10);
        });

        return ExpenseResource::collection($expenses);

    }

    public function store(Request $request)
    {
        // Get the logged-in user - be it admin,manager or employee
        $user = $request->user();

        // Validation rules
        $rules = [
            'title'=> 'required|string|max:255',
            'amount'=> 'required|numeric',
            'category'=> 'required|string|max:100',
        ];

        $messages = [
            'title.required' => 'Expense title is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric'  => 'Amount must be a number.',
            'category.required' => 'Category is required.',
        ];

        // Create validator
        $validator = Validator::make($request->all(), $rules, $messages);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        // Retrieve validated data
        $data = $validator->validated();


        // Attach user & company data
        $data['company_id'] = $user->company_id;
        $data['user_id']    = $user->id;

        // Create the expense
        $expense = Expense::create($data);

        //clear cache
        $cacheKey = implode(':', [
            'expenses',
            $user->company_id,
            'search', md5(''),
            'page',   1,
            'perPage',10,
        ]);

        Cache::forget($cacheKey);

        return new ExpenseResource($expense);
    }

    public function update(Request $request, $id)
    {
        // Get the logged-in user - be it admin,manager
        $user = $request->user();

        $expense = Expense::findOrFail($id);

        // Verify that the expense belongs to the same company as the user
        if ($expense->company_id !== $user->company_id) {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        // Check user role
        if (!in_array($user->role, ['Admin', 'Manager'])) {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        $validator = Validator::make($request->all(), [
            'title'    => 'sometimes|string|max:255',
            'amount'   => 'sometimes|numeric',
            'category' => 'sometimes|string|max:100',
        ], [
            'title.string'    => 'Title must be text.',
            'title.max'       => 'Title cannot exceed 255 characters.',
            'amount.numeric'  => 'Amount must be a number.',
            'category.string' => 'Category must be text.',
            'category.max'    => 'Category cannot exceed 100 characters.',
        ]);

        if ($validator->fails()) {
            return response()->apiValidationError($validator);
        }

        $data = $validator->validated();

        $expense->update($data);
        return new ExpenseResource($expense);
    }

    public function destroy(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $user = $request->user();

        if ($expense->company_id !== $user->company_id) {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        if ($user->role !== 'Admin') {
            return response()->apiError(Constant::AUTHORIZATION_ERROR,'Unauthorized',403);
        }

        $expense->delete();

        return response()->apiSuccess([],'Expense deleted ');

    }

}
