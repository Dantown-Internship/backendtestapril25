<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Database\Factories\ExpenseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;


class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $manager;
    public $company;
    public $employee;


    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id
        ]);
        $this->employee = User::factory()->create([
            'company_id' => $this->company->id
        ]);
        $this->manager = User::factory()->create([
            'company_id' => $this->company->id
        ]);

        $this->user->assignRole(RoleEnum::ADMIN);
        $this->employee->assignRole(RoleEnum::EMPLOYEE);
        $this->manager->assignRole(RoleEnum::MANAGER);
        Sanctum::actingAs($this->user, ['*']);
    }


    /** @test */
    public function it_creates_an_expense_successfully()
    {

        $this->actingAs($this->user);

        // Prepare the request data
        $data = [
            'amount' => "100.00",
            'title' => 'Office Supplies',
            'category' => 'Office',
            'company_id' => (string) $this->company->id,
        ];


        $response = $this->postJson('/api/expenses', $data);

        // Assert that the response is correct
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Expense created successfully.',
            ]);

        // Assert that the expense is in the database
        $this->assertDatabaseHas('expenses', [
            'amount' => 100.00,
            'title' => 'Office Supplies',
            'category' => 'Office',
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);
    }



    /** @test */
    public function it_creates_a_user_and_user_creates_expense_successfully()
    {
        $this->actingAs($this->user);


        $createUserData = [
            'name' => 'John Doe',
            'email' => 'john4@example.com',
            'company_id' => (string) $this->company->id,
            'password' => 'password123',
            'role' => 'EMPLOYEE',
        ];

        $createUserResponse = $this->postJson('/api/users', $createUserData);

        $createUserResponse->assertStatus(201);

        $createdUser = User::where('email', 'john4@example.com')->first();
        $this->assertNotNull($createdUser);

        // Act as the newly created user
        $this->actingAs($createdUser);




        $expenseData = Expense::factory()->create([
            'company_id' => (string) $this->company->id,
            'user_id' => (string) $createdUser->id
        ]);


        $expenseResponse = $this->postJson('/api/expenses', $expenseData->toArray());

        //  Assert the expense creation response
        $expenseResponse->assertStatus(201)
            ->assertJson([
                'message' => 'Expense created successfully.',
            ]);

        // Step 5: Assert the expense is in the database
        $this->assertDatabaseHas('expenses', [
            'amount' => $expenseData->amount,
            'title' => $expenseData->title,
            'category' => $expenseData->category,
            'company_id' => $this->company->id,
            'user_id' => $createdUser->id,
        ]);
    }



    /** @test */
    public function user_can_get_only_their_own_expenses()
    {
        $user = User::factory()->create(['company_id' => $this->company->id]);

        //
        Expense::factory()->count(5)->create(['user_id' => $user->id, 'company_id' => $this->company->id]);


        // Acting as the normal user
        $this->actingAs($user);

        // Get expenses
        $response = $this->getJson('/api/expenses');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_update_expense_as_manager_or_admin()
    {
        // Act as a admin user
        $this->actingAs($this->user);


        // Create an expense
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id
        ]);

        // Data for updating the expense
        $updateData = [
            'amount' => '200.00',
            'title' => 'Updated Office Supplies',
            'category' => 'Updated Office',
        ];

        // Send PUT request to update the expense
        $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

        // Assert that the response status is 200 OK
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Expense updated successfully.',
            ]);

        // Assert the expense is updated in the database
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'amount' => '200.00',
            'title' => 'Updated Office Supplies',
            'category' => 'Updated Office',
        ]);
    }

    /** @test */
    // public function it_cannot_update_expense_as_non_manager_user()
    // {
    //     // Act as a non-manager user (employee)
    //     $this->actingAs($this->employee);

    //     // Create an expense
    //     $expense = Expense::factory()->create([
    //         'company_id' => $this->company->id,
    //         'user_id' => $this->employee->id
    //     ]);

    //     // Data for updating the expense
    //     $updateData = [
    //         'amount' => '200.00',
    //         'title' => 'Updated Office Supplies',
    //         'category' => 'Updated Office',
    //     ];

    //     // Send PUT request to update the expense
    //     $response = $this->putJson("/api/expenses/{$expense->id}", $updateData);

    //     // Assert that the response status is 403 Forbidden
    //     $response->assertStatus(403)
    //         ->assertJson([
    //             'message' => 'You do not have permission to update this expense.',
    //         ]);
    // }



    /** @test */
    public function it_can_delete_expense_as_admin()
    {
        // Act as an admin user
        $this->actingAs($this->user);

        // Create an expense
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->manager->id
        ]);

        // Send DELETE request to delete the expense
        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        // Assert that the response status is 200 OK
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'expense deleted successfully',
            ]);

        // Assert the expense is deleted from the database
        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    /** @test */
    public function it_cannot_delete_expense_as_non_admin_user()
    {
        // Act as a non-admin user (manager or employee)
        $this->actingAs($this->manager);

        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $this->manager->id
        ]);


        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        // Assert
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to delete this expense.',
            ]);
    }
}
