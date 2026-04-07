<?php

namespace Tests\Unit\Policies;

use App\Models\Lead;
use App\Models\User;
use App\Policies\LeadPolicy;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadPolicyTest extends TestCase
{
    use RefreshDatabase;

    private LeadPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->policy = new LeadPolicy();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    // --- viewAny / view ---

    public function test_account_manager_can_view_any_leads(): void
    {
        $this->assertTrue($this->policy->viewAny($this->userWithRole('Account Manager')));
    }

    public function test_developer_cannot_view_leads(): void
    {
        $this->assertFalse($this->policy->viewAny($this->userWithRole('Developer')));
    }

    public function test_designer_cannot_view_leads(): void
    {
        $this->assertFalse($this->policy->viewAny($this->userWithRole('Designer')));
    }

    // --- create ---

    public function test_account_manager_can_create_lead(): void
    {
        $this->assertTrue($this->policy->create($this->userWithRole('Account Manager')));
    }

    public function test_project_manager_cannot_create_lead(): void
    {
        $this->assertFalse($this->policy->create($this->userWithRole('Project Manager')));
    }

    // --- update ---

    public function test_admin_can_update_any_lead(): void
    {
        $admin = $this->userWithRole('Admin');
        $lead  = Lead::factory()->create();

        $this->assertTrue($this->policy->update($admin, $lead));
    }

    public function test_account_manager_can_update_own_assigned_lead(): void
    {
        $am   = $this->userWithRole('Account Manager');
        $lead = Lead::factory()->create(['assigned_to' => $am->id]);

        $this->assertTrue($this->policy->update($am, $lead));
    }

    public function test_account_manager_cannot_update_lead_assigned_to_others(): void
    {
        $am1  = $this->userWithRole('Account Manager');
        $am2  = $this->userWithRole('Account Manager');
        $lead = Lead::factory()->create(['assigned_to' => $am2->id]);

        $this->assertFalse($this->policy->update($am1, $lead));
    }

    // --- delete ---

    public function test_admin_can_delete_lead(): void
    {
        $admin = $this->userWithRole('Admin');
        $lead  = Lead::factory()->create();

        $this->assertTrue($this->policy->delete($admin, $lead));
    }

    public function test_account_manager_cannot_delete_lead(): void
    {
        $am   = $this->userWithRole('Account Manager');
        $lead = Lead::factory()->create(['assigned_to' => $am->id]);

        $this->assertFalse($this->policy->delete($am, $lead));
    }
}
