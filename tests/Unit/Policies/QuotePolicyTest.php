<?php

namespace Tests\Unit\Policies;

use App\Models\Quote;
use App\Models\User;
use App\Policies\QuotePolicy;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotePolicyTest extends TestCase
{
    use RefreshDatabase;

    private QuotePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->policy = new QuotePolicy();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    // --- viewAny / view ---

    public function test_account_manager_can_view_any_quotes(): void
    {
        $this->assertTrue($this->policy->viewAny($this->userWithRole('Account Manager')));
    }

    public function test_developer_cannot_view_quotes(): void
    {
        $this->assertFalse($this->policy->viewAny($this->userWithRole('Developer')));
    }

    // --- create ---

    public function test_account_manager_can_create_quote(): void
    {
        $this->assertTrue($this->policy->create($this->userWithRole('Account Manager')));
    }

    public function test_project_manager_cannot_create_quote(): void
    {
        $this->assertFalse($this->policy->create($this->userWithRole('Project Manager')));
    }

    // --- update ---

    public function test_admin_can_update_any_quote(): void
    {
        $admin = $this->userWithRole('Admin');
        $quote = Quote::factory()->create();

        $this->assertTrue($this->policy->update($admin, $quote));
    }

    public function test_account_manager_can_update_draft_quote(): void
    {
        $am    = $this->userWithRole('Account Manager');
        $quote = Quote::factory()->draft()->create();

        $this->assertTrue($this->policy->update($am, $quote));
    }

    public function test_account_manager_can_update_quote_they_created(): void
    {
        $am    = $this->userWithRole('Account Manager');
        $quote = Quote::factory()->sent()->create(['created_by' => $am->id]);

        $this->assertTrue($this->policy->update($am, $quote));
    }

    public function test_account_manager_cannot_update_sent_quote_from_another_user(): void
    {
        $am1   = $this->userWithRole('Account Manager');
        $am2   = $this->userWithRole('Account Manager');
        $quote = Quote::factory()->sent()->create(['created_by' => $am2->id]);

        $this->assertFalse($this->policy->update($am1, $quote));
    }

    public function test_account_manager_cannot_update_approved_quote(): void
    {
        $am    = $this->userWithRole('Account Manager');
        $am2   = $this->userWithRole('Account Manager');
        $quote = Quote::factory()->approved()->create(['created_by' => $am2->id]);

        $this->assertFalse($this->policy->update($am, $quote));
    }

    // --- delete ---

    public function test_admin_can_delete_quote(): void
    {
        $admin = $this->userWithRole('Admin');
        $quote = Quote::factory()->create();

        $this->assertTrue($this->policy->delete($admin, $quote));
    }

    public function test_account_manager_cannot_delete_quote(): void
    {
        $am    = $this->userWithRole('Account Manager');
        $quote = Quote::factory()->create(['created_by' => $am->id]);

        $this->assertFalse($this->policy->delete($am, $quote));
    }

    // --- forceDelete ---

    public function test_only_super_admin_can_force_delete_quote(): void
    {
        $admin = $this->userWithRole('Admin');
        $quote = Quote::factory()->create();

        $this->assertFalse($this->policy->forceDelete($admin, $quote));
    }
}
