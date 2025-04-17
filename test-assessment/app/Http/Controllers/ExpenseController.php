<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\User;
use App\Services\DataTableService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class ExpenseController extends Controller
{
    use ControllerTrait;
    public $request;
    protected $dataTableService;

    public function __construct(Request $request, DataTableService $dataTableService)
    {
        $this->request = $request;
        $this->dataTableService = $dataTableService;
    }

    public function index()
    {
        $customQuery = Expense::where('company_id',  Auth::user()->company_id)
        ->where('user_id', Auth::user()->id)
        ->with('company');
        $columns = [];
        $filter = ['title', 'category'];
        $additionalColumns = [];
        $data = $this->dataTableService->generate($customQuery, null, $this->request, $columns, $filter, $additionalColumns, 'created_at', 'created_at');
        return $this->successResponse('Records Fetched', $data, Response::HTTP_OK);
    }

    public function store(){
        $validator = $this->validateExpenseRequest($this->request->all());
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors()->first(), null, Response::HTTP_BAD_REQUEST);
        }
        $data = Expense::create([
            'company_id'=>Auth::user()->company_id,
            'user_id'=>Auth::user()->id,
            'title'=>$this->request->title,
            'amount'=>$this->request->amount,
            'category'=>$this->request->category,
        ]);
        return $this->successResponse('Records created', $data, Response::HTTP_OK);

    }

    public function single($id){
        try {
            $validator = $this->validateExpenseRequest($this->request->all());
            if ($validator->fails()) {
                return $this->failureResponse($validator->errors()->first(), null, Response::HTTP_BAD_REQUEST);
            }
            $expense = Expense::where('id',$id)->where('company_id',  Auth::user()->company_id)->first();
            $expense->title=$this->request->title;
            $expense->amount=$this->request->amount;
            $expense->category=$this->request->category;
            $expense->update();
            return $this->successResponse('Records created', $expense, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->failureResponse('unable to update', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function SoftDeletes($id)
    {
        try {
            $expense = Expense::where('id',$id)->first();
            if($expense->delete()){
                return $this->successResponse('expense successfully delated.',[], Response::HTTP_OK);
            }
        } catch (\Throwable $th) {
            return $this->failureResponse('unable to delate', [], Response::HTTP_BAD_REQUEST);
        }

    }

    public function validateExpenseRequest()
    {
        return Validator::make($this->request->all(), [
            'title'=>['required'],
            'amount'=>['required'],
            'category'=>['required'],
        ]);
    }
}
