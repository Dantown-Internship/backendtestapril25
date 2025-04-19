<?php

namespace App\Data;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public ?User $user = null,
    ) {}

    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public static function stopOnFirstFailure(): bool
    {
        return true;
    }

    /**
     * @throws ValidationException
     */
    public static function fromRequest(Request $request): self
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return new self(
            email: $data['email'],
            password: $data['password'],
            user: $user
        );
    }
}
