<?php

namespace Tests\Unit\Policies;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ProjectPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->policy = new ProjectPolicy();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function makeProject(User $manager): Project
    {
        return Project::factory()->create([
            'client_id'          => Client::factory()->create()->id,
            'project_manager_id' => $manager->id,
        ]);
    }

    // --- viewAny ---

    public function test_admin_can_view_any_projects(): void
    {
        $this->assertTrue($this->policy->viewAny($this->userWithRole('Admin')));
    }

    public function test_project_manager_can_view_any_projects(): void
    {
        $this->assertTrue($this->policy->viewAny($this->userWithRole('Project Manager')));
    }

    public function test_developer_can_view_any_projects(): void
    {
        $this->assertTrue($this->policy->viewAny($this->userWithRole('Developer')));
    }

    // --- view ---

    public function test_admin_can_view_any_project(): void
    {
        $admin   = $this->userWithRole('Admin');
        $manager = $this->userWithRole('Project Manager');
        $project = $this->makeProject($manager);

        $this->assertTrue($this->policy->view($admin, $project));
    }

    public function test_project_manager_can_view_all_projects(): void
    {
        $pm      = $this->userWithRole('Project Manager');
        $manager = $this->userWithRole('Project Manager');
        $project = $this->makeProject($manager);

        $this->assertTrue($this->policy->view($pm, $project));
    }

    public function test_developer_cannot_view_project_they_are_not_member_of(): void
    {
        $developer = $this->userWithRole('Developer');
        $manager   = $this->userWithRole('Project Manager');
        $project   = $this->makeProject($manager);

        $this->assertFalse($this->policy->view($developer, $project));
    }

    public function test_developer_can_view_project_they_are_member_of(): void
    {
        $developer = $this->userWithRole('Developer');
        $manager   = $this->userWithRole('Project Manager');
        $project   = $this->makeProject($manager);

        $project->members()->attach($developer->id, ['role' => 'developer', 'joined_at' => now()]);

        $this->assertTrue($this->policy->view($developer, $project));
    }

    // --- create ---

    public function test_project_manager_can_create_project(): void
    {
        $this->assertTrue($this->policy->create($this->userWithRole('Project Manager')));
    }

    public function test_developer_cannot_create_project(): void
    {
        $this->assertFalse($this->policy->create($this->userWithRole('Developer')));
    }

    public function test_designer_cannot_create_project(): void
    {
        $this->assertFalse($this->policy->create($this->userWithRole('Designer')));
    }

    // --- update ---

    public function test_admin_can_update_any_project(): void
    {
        $admin   = $this->userWithRole('Admin');
        $manager = $this->userWithRole('Project Manager');
        $project = $this->makeProject($manager);

        $this->assertTrue($this->policy->update($admin, $project));
    }

    public function test_project_manager_can_update_own_project(): void
    {
        $pm      = $this->userWithRole('Project Manager');
        $project = $this->makeProject($pm);

        $this->assertTrue($this->policy->update($pm, $project));
    }

    public function test_project_manager_cannot_update_another_managers_project(): void
    {
        $pm1     = $this->userWithRole('Project Manager');
        $pm2     = $this->userWithRole('Project Manager');
        $project = $this->makeProject($pm2);

        $this->assertFalse($this->policy->update($pm1, $project));
    }

    // --- delete / forceDelete ---

    public function test_admin_can_delete_project(): void
    {
        $admin   = $this->userWithRole('Admin');
        $manager = $this->userWithRole('Project Manager');
        $project = $this->makeProject($manager);

        $this->assertTrue($this->policy->delete($admin, $project));
    }

    public function test_project_manager_cannot_delete_project(): void
    {
        $pm      = $this->userWithRole('Project Manager');
        $project = $this->makeProject($pm);

        $this->assertFalse($this->policy->delete($pm, $project));
    }

    public function test_only_super_admin_can_force_delete(): void
    {
        $admin   = $this->userWithRole('Admin');
        $manager = $this->userWithRole('Project Manager');
        $project = $this->makeProject($manager);

        // Admin cannot force-delete (Super Admin bypass is handled by Gate::before, not the policy)
        $this->assertFalse($this->policy->forceDelete($admin, $project));
    }
}
