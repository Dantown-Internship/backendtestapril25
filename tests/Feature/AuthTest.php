<?php
// ✅ Feature Test 1: Authentication (Register & Login)
// This test will check the registration and login functionality for different user roles.

use App\Enums\Roles;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login'); // Reset any rate limit if applied during testing
    RateLimiter::clear('register');
});

// test('example', function () {
//     $response = $this->get('/');

//     $response->assertStatus(200);
// });


// 🚀 Register test - Admin only
it('allows admin to register a user', function () {
    $company = Company::factory()->create();
    $admin = User::factory()->create([
        'role' => Roles::ADMIN,
        'company_id' => $company->id,
    ]);

    $token = $admin->createToken('auth')->plainTextToken;

    $randomizer = bin2hex(random_bytes(4));

    $response = $this->withToken($token)->postJson('/api/register', [
        'name' => 'New User',
        'email' => "{$randomizer}newuser@example.com",
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_id' => $company->id,
        'role' => Roles::EMPLOYEE, // Use the enum for role
    ]);

    $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
});

// ❌ Rejects non-admin registration attempt
it('prevents non-admins from registering a user', function () {
    $company = Company::factory()->create();
    $employee = User::factory()->create([
        'role' => Roles::EMPLOYEE, // Use the enum for role
        'company_id' => $company->id,
    ]);

    $token = $employee->createToken('auth')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/register', [
        'name' => 'Another User',
        'email' => 'unauthorized@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'company_id' => $company->id,
        'role' => Roles::MANAGER, // Use the enum for role
    ]);

    $response->assertStatus(403);
});

// ✅ Successful login
it('logs in with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
});

// ❌ Invalid login
it('fails login with incorrect credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
});
