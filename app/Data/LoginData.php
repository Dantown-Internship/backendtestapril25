<?php

namespace App\Data;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
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

    public static function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            if (!auth()->attempt($data)) {
                $validator->errors()->add('email', 'Invalid email or password');
            }

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $data['user'] = $user;
        });
    }
}
