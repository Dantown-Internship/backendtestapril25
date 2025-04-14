<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use GuzzleHttp\Promise\Create;
use App\Libs\Actions\Users\UpdateUserAction;
use App\Libs\Actions\Users\GetUsersAction;
use App\Libs\Actions\Users\CreateUserAction;
use App\Http\Controllers\Controller;

class UserController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string|in:user,admin,manager',
        ]);

        return $this->updateUserAction->handle($request, $id);
    }
}
