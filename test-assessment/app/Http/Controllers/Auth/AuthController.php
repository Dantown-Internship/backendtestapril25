<?php

namespace App\Http\Controllers\Auth;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataTableService;
use App\Traits\ControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class AuthController extends Controller
{
    use ControllerTrait;
    public $request;
    protected $dataTableService;

    public function __construct(Request $request, DataTableService $dataTableService)
    {
        $this->request = $request;
        $this->dataTableService = $dataTableService;
    }


    public function index(){
        $customQuery = User::query();
        $columns = [];
        $filter = ['email', 'name','role'];
        $additionalColumns = [];
        $data = $this->dataTableService->generate($customQuery, null, $this->request, $columns, $filter, $additionalColumns, 'created_at', 'created_at');
        return $this->successResponse('Records Fetched', $data, Response::HTTP_OK);
    }

    public function register()
    {
        $validator = $this->validateRequestRegister($this->request->all());
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors()->first(), null, Response::HTTP_BAD_REQUEST);
        }
        $data = $this->request->all();
         $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'company_id' => $data['company_id'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::Admin,
        ]);
        return $this->successResponse('user successfully created .', $user, Response::HTTP_OK);

    }

    public function Login()
    {
        $validator = $this->validateRequestLogin($this->request->all());
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors()->first(), null, Response::HTTP_BAD_REQUEST);
        }
        if (!Auth::attempt($this->request->only('email', 'password'))) {
            return $this->failureResponse('Invalid credentails', null, Response::HTTP_BAD_REQUEST);
        }
        $user = User::where('email', $this->request['email'])->firstOrFail();
        if ($user && Hash::check($this->request->password, $user->password)) {
            $token = $user->createToken('API TOKEN')->plainTextToken;
            return $this->successResponse("Login Successful", [
                'user'=>$user,
                'token'=>$token
            ], Response::HTTP_OK);
        }
    }

    public function validateRequestRegister()
    {
        return Validator::make($this->request->all(), [
            'name' => ['required', 'string'],
            'company_id' => ['required', 'integer'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters() 
                    ->mixedCase()
                    ->numbers() 
                    ->symbols() 
            ]
        ]);
    }
    public function validateRequestLogin()
    {
        return Validator::make($this->request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
    }


    public function addUser(){
        $validator = $this->validateRequestAddUser($this->request->all());
        if ($validator->fails()) {
            return $this->failureResponse($validator->errors()->first(), null, Response::HTTP_BAD_REQUEST);
        }
        $data = $this->request->all();
         $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'company_id' => Auth::user()->company_id,
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
        return $this->successResponse('user successfully created .', $user, Response::HTTP_OK);
        
    }
    public function validateRequestAddUser()
    {
        return Validator::make($this->request->all(), [
            'name' => ['required', 'string'],
            // 'company_id' => ['required', 'integer'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => [
                'required',
                Rule::in(
                    collect(UserRole::cases())
                        ->reject(fn($role) => $role === UserRole::Admin)
                        ->map(fn($role) => $role->value)
                ),
            ],
    
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters() 
                    ->mixedCase()
                    ->numbers() 
                    ->symbols() 
            ]
        ]);
    }

    public function updateUser($id){
        try {
            $validator = $this->validateUserRequest($this->request->all());
            if ($validator->fails()) {
                return $this->failureResponse($validator->errors()->first(), null, Response::HTTP_BAD_REQUEST);
            }
            $user = User::where('id',$id)
            ->where('role','!=', 'Admin')
            ->where('company_id',Auth::user()->company_id)
            ->first();
            $user->name=$this->request->name;
            $user->email=$this->request->email;
            $user->company_id=$this->request->company_id;
            $user->role=$this->request->role;
            $user->update();
            return $this->successResponse('user record successfully updated', $user, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->failureResponse('Unable to update user record', [], Response::HTTP_BAD_REQUEST);
        }
    }

    public function validateUserRequest()
    {
        return Validator::make($this->request->all(), [
            'name' => ['required', 'string'],
            'company_id' => ['required', 'integer'],
            'role' => [
                'required',
                Rule::in(
                    collect(UserRole::cases())
                        ->reject(fn($role) => $role === UserRole::Admin)
                        ->map(fn($role) => $role->value)
                ),
            ],
        ]);
    }
}
