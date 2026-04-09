<?php

namespace Tests\Unit\Policies;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectTask;
use App\Models\User;
use App\Policies\ProjectTaskPolicy;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ProjectTaskPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $this->policy = new ProjectTaskPolicy();
    }

    public function test_admin_can_view_and_update_any_task(): void
    {
        [$admin, $task] = $this->makeTaskContext('Admin');

        $this->assertTrue($this->policy->view($admin, $task));
        $this->assertTrue($this->policy->updateStatus($admin, $task));
    }

    public function test_project_manager_can_view_and_update_managed_project_task(): void
    {
        [$manager, $task] = $this->makeTaskContext('Project Manager', managerOwnsProject: true);

        $this->assertTrue($this->policy->view($manager, $task));
        $this->assertTrue($this->policy->updateStatus($manager, $task));
    }

    public function test_project_manager_cannot_view_or_update_unmanaged_project_task(): void
    {
        [$manager, $task] = $this->makeTaskContext('Project Manager', managerOwnsProject: false);

        $this->assertFalse($this->policy->view($manager, $task));
        $this->assertFalse($this->policy->updateStatus($manager, $task));
    }

    public function test_team_member_can_view_team_task_in_member_project_but_only_update_own_task(): void
    {
        [$developer, $task] = $this->makeTaskContext('Developer', memberOfProject: true, assignedToUser: false);

        $this->assertTrue($this->policy->view($developer, $task));
        $this->assertFalse($this->policy->updateStatus($developer, $task));
    }

    public function test_team_member_can_update_own_task(): void
    {
        [$designer, $task] = $this->makeTaskContext('Designer', memberOfProject: true, assignedToUser: true);

        $this->assertTrue($this->policy->view($designer, $task));
        $this->assertTrue($this->policy->updateStatus($designer, $task));
    }

    private function makeTaskContext(
        string $role,
        bool $managerOwnsProject = true,
        bool $memberOfProject = false,
        bool $assignedToUser = false,
    ): array {
        $user = User::factory()->create();
        $user->assignRole($role);

        $manager = User::factory()->create();
        $client = Client::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'project_manager_id' => $managerOwnsProject ? $user->id : $manager->id,
        ]);

        if ($memberOfProject) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role' => 'developer',
            ]);
        }

        $assignee = $assignedToUser ? $user : User::factory()->create();

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'assigned_to' => $assignee->id,
        ]);

        return [$user, $task];
    }
}