<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use GuzzleHttp\Promise\Create;
use App\Models\User;
use App\Libs\Actions\Users\UpdateUserAction;
use App\Libs\Actions\Users\GetUsersAction;
use App\Libs\Actions\Users\CreateUserAction;
use App\Http\Controllers\Controller;

final class UserController extends Controller
{
    public function __construct(
        protected CreateUserAction $createUserAction,
        protected GetUsersAction $getUsersAction,
        protected UpdateUserAction $updateUserAction
    ){}

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->getUsersAction->handle($request);
    }

    /**
     * Create New User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:user,admin,manager',
        ]);

        return $this->createUserAction->handle($request);
    }

    /**
     * Update A User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * 
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|unique:users,email,{$user->id}",
            'role' => 'sometimes|required|string|in:employee,admin,manager',
        ]);

        if($request->has('role') && !Gate::allows('update-role', auth()->user())){
            return response()->json([
                'message' => 'You are unauthorized to update a user\'s role',
                'success' => false
            ]);
        }

        return $this->updateUserAction->handle($request, $user->id);
    }
}
