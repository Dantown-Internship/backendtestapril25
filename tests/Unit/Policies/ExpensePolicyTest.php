<?php

namespace Tests\Unit;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use App\Policies\ExpensePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpensePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected ExpensePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ExpensePolicy();
    }

    public function test_admin_cannot_access_expense_from_another_company()
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $admin = User::factory()->for($companyA)->create(["role" => RoleEnum::ADMIN()]);
        $expense = Expense::factory()
            ->for(User::factory()->for($companyB), 'owner')
            ->for($companyB, 'company')
            ->create();

        $this->assertFalse($this->policy->before($admin, 'view', $expense));
    }

    public function test_admin_can_view_any_expense_in_same_company()
    {
        $company = Company::factory()->create();

        $user = User::factory()->for($company)->create([
            'role' => RoleEnum::ADMIN(),
        ]);

        $expense = Expense::factory()->for($user, 'owner')->for($company, 'company')->create();

        $this->assertTrue($this->policy->view($user, $expense));
    }

    public function test_owner_can_view_their_own_expense()
    {
        $company = Company::factory()->create();
        $user = User::factory()->for($company)->create();

        $expense = Expense::factory()->for($user, 'owner')->for($company, 'company')->create();

        $this->assertTrue($this->policy->view($user, $expense));
    }

    public function test_employee_cannot_update_expense()
    {
        $company = Company::factory()->create();

        $user = User::factory()->for($company)->create([
            'role' => RoleEnum::EMPLOYEE(),
        ]);

        $expense = Expense::factory()->for($user, 'owner')->for($company, 'company')->create();

        $this->assertFalse($this->policy->update($user, $expense));
    }

    public function test_manager_can_update_expense()
    {
        $company = Company::factory()->create();

        $user = User::factory()->for($company)->create([
            'role' => RoleEnum::MANAGER(),
        ]);

        $expense = Expense::factory()->for($user, 'owner')->for($company, 'company')->create();

        $this->assertTrue($this->policy->update($user, $expense));
    }

    public function test_admin_can_update_expense()
    {
        $company = Company::factory()->create();

        $admin = User::factory()->for($company)->create([
            'role' => RoleEnum::ADMIN(),
        ]);

        $expense = Expense::factory()
            ->for($admin, 'owner')
            ->for($company, 'company')
            ->create();

        $this->assertTrue($this->policy->update($admin, $expense));
    }

    public function test_admin_can_delete_expense()
    {
        $company = Company::factory()->create();

        $user = User::factory()->for($company)->create([
            'role' => RoleEnum::ADMIN(),
        ]);

        $expense = Expense::factory()->for($user, 'owner')->for($company, 'company')->create();

        $this->assertTrue($this->policy->delete($user, $expense));
    }

    public function test_manager_cannot_delete_expense()
    {
        $company = Company::factory()->create();

        $user = User::factory()->for($company)->create([
            'role' => RoleEnum::MANAGER(),
        ]);

        $expense = Expense::factory()->for($user, 'owner')->for($company, 'company')->create();

        $this->assertFalse($this->policy->delete($user, $expense));
    }
}
