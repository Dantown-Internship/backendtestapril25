<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        if(auth()->user()->cannot('view', User::class)) {
            return $this->forbidden();
        }
        return $this->success([
            'users' => User::query()->paginate(20)
        ]);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'role' => [new Enum(RoleEnum::class)],
                'password' => 'required|string|min:8',
            ]
        );
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random()),
            'company_id' => auth()->user()->company_id,
            'role' => $request->role,
        ]);

        return $this->success([
            'user' => $user,
        ], 'user account creation succesful');
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        if(auth()->user()->cannot('update', $user)) {
            return $this->forbidden();
        }
        $user->role = $request->role;
        $user->save();
        return $this->success([
            'user' => $user
        ]);
    }
}