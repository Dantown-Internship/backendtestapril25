<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
        ]);

        Sanctum::actingAs($this->user, ['*']);
    }

    /** @test */
    public function it_lists_expenses_with_pagination_and_caching()
    {
        Expense::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/expenses?per_page=10');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'meta' => [
                    'expenses' => [
                        'data',
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                    ]
                ]
            ]);

        // Check Redis cache exists
        $search = '';
        $page = 1;
        $perPage = 10;

        $cacheKey = "expenses_{$this->company->id}_{$search}_page_{$page}_{$perPage}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    /** @test */
    public function it_creates_an_expense_and_flushes_cache()
    {
        Cache::put('dummy_key', 'value');

        $payload = [
            'title' => 'Fuel',
            'amount' => 5000,
            'category' => 'Transport'
        ];

        $response = $this->postJson('/api/expenses', $payload);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'Fuel']);

        $this->assertDatabaseHas([
            'message',
            'meta' => [
                'expenses' =>
                [
                    'title' => 'Fuel',
                    'amount' => 5000,
                    'category' => 'Transport',
                    'user_id' => $this->user->id,
                    'company_id' => $this->company->id,
                ]
            ]
        ]);

        $this->assertFalse(Cache::has('dummy_key'), 'Cache was not flushed on creation');
    }

    /** @test */
    public function it_updates_an_expense_and_logs_audit()
    {
        $expense = Expense::factory()->create([
            'title' => 'Lunch',
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $payload = [
            'title' => 'Dinner',
            'amount' => 800,
            'category' => 'Food'
        ];

        $response = $this->putJson("/api/expenses/{$expense->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Dinner']);
        $this->assertDatabaseHas(
            [
                'message',
                'meta' => [
                    'expenses' =>
                    [
                        'id' => $expense->id,
                        'title' => 'Dinner',
                    ]
                ]
            ]
        );
    }

    /** @test */
    public function it_deletes_an_expense_and_logs_audit()
    {
        $expense = Expense::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
        ]);

        $response = $this->deleteJson("/api/expenses/{$expense->id}");

        $response->assertOk()->assertJson(['message' => 'Expense deleted.']);

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }



    /** @test */
    public function it_denies_access_to_expenses_of_other_companies()
    {
        $otherExpense = Expense::factory()->create();

        $response = $this->putJson("/api/expenses/{$otherExpense->id}", [
            'title' => 'Hacked',
            'amount' => 10,
            'category' => 'Security'
        ]);

        $response->assertNotFound();
    }
}