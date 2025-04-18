<?php

namespace Tests\Unit;

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    public function test_admin_can_perform_all_actions_on_same_company_user()
    {
        $company = Company::factory()->create();

        $admin = User::factory()->for($company)->create(['role' => RoleEnum::ADMIN()]);
        $target = User::factory()->for($company)->create();

        $this->assertTrue($this->policy->viewAny($admin));
        $this->assertTrue($this->policy->view($admin, $target));
        $this->assertTrue($this->policy->create($admin));
        $this->assertTrue($this->policy->update($admin, $target));
        $this->assertTrue($this->policy->delete($admin, $target));
        $this->assertTrue($this->policy->restore($admin, $target));
        $this->assertTrue($this->policy->forceDelete($admin, $target));
    }

    public function test_admin_cannot_access_user_in_different_company()
    {
        $adminCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();

        $admin = User::factory()->for($adminCompany)->create(['role' => RoleEnum::ADMIN()]);
        $target = User::factory()->for($otherCompany)->create();

        $this->assertFalse($this->policy->before($admin, 'view', $target));
    }

    public function test_employee_and_manager_cannot_perform_admin_actions()
    {
        $company = Company::factory()->create();
        $target = User::factory()->for($company)->create();

        $employee = User::factory()->for($company)->create(['role' => RoleEnum::EMPLOYEE()]);
        $manager  = User::factory()->for($company)->create(['role' => RoleEnum::MANAGER()]);

        foreach ([$employee, $manager] as $user) {
            $this->assertFalse($this->policy->viewAny($user));
            $this->assertFalse($this->policy->create($user));
            $this->assertFalse($this->policy->update($user, $target));
            $this->assertFalse($this->policy->delete($user, $target));
            $this->assertFalse($this->policy->restore($user, $target));
            $this->assertFalse($this->policy->forceDelete($user, $target));
        }
    }

    public function test_user_can_view_self()
    {
        $company = Company::factory()->create();

        $user = User::factory()->for($company)->create();

        $this->assertTrue($this->policy->view($user, $user));
    }
}
