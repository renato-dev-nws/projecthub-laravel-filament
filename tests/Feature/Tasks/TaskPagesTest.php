<?php

namespace Tests\Feature\Tasks;

use App\Filament\TeamPanel\Clusters\Tasks\Pages\TaskKanban;
use App\Filament\TeamPanel\Clusters\Tasks\Pages\TaskList;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectTask;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_team_member_list_page_shows_only_own_tasks_by_default(): void
    {
        [$developer, $project] = $this->makeProjectContext('Developer');

        $ownTask = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'assigned_to' => $developer->id,
            'title' => 'Minha tarefa',
        ]);

        ProjectTask::factory()->create([
            'project_id' => $project->id,
            'title' => 'Tarefa de outro membro',
        ]);

        Livewire::actingAs($developer)
            ->test(TaskList::class)
            ->assertSee($ownTask->title)
            ->assertDontSee('Tarefa de outro membro');
    }

    public function test_team_member_can_switch_to_team_scope_when_project_is_selected(): void
    {
        [$developer, $project] = $this->makeProjectContext('Developer');

        ProjectTask::factory()->create([
            'project_id' => $project->id,
            'assigned_to' => $developer->id,
            'title' => 'Minha tarefa',
        ]);

        $otherTask = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'title' => 'Tarefa da equipe',
        ]);

        Livewire::actingAs($developer)
            ->test(TaskList::class)
            ->set('projectId', $project->id)
            ->set('scope', 'team')
            ->assertSee($otherTask->title);
    }

    public function test_team_member_cannot_update_other_member_task_status(): void
    {
        [$developer, $project] = $this->makeProjectContext('Developer');

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'assigned_to' => User::factory()->create()->id,
            'status' => 'todo',
        ]);

        Livewire::actingAs($developer)
            ->test(TaskList::class)
            ->call('updateTaskStatus', $task->id, 'done');

        $this->assertSame('todo', $task->fresh()->status);
    }

    public function test_project_manager_can_filter_and_update_team_tasks(): void
    {
        [$manager, $project] = $this->makeProjectContext('Project Manager', true);
        $member = User::factory()->create();
        $member->assignRole('Developer');

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $member->id,
            'role' => 'developer',
        ]);

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'assigned_to' => $member->id,
            'title' => 'Tarefa filtrável',
            'status' => 'todo',
        ]);

        Livewire::actingAs($manager)
            ->test(TaskList::class)
            ->set('memberId', $member->id)
            ->assertSee($task->title)
            ->call('updateTaskStatus', $task->id, 'done');

        $this->assertSame('done', $task->fresh()->status);
    }

    public function test_admin_can_access_kanban_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->get(TaskKanban::getUrl(panel: 'admin'));

        $response->assertSuccessful();
        $response->assertSee('Kanban de Tarefas');
    }

    private function makeProjectContext(string $role, bool $managerOwnsProject = false): array
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        $client = Client::factory()->create();
        $manager = $managerOwnsProject ? $user : User::factory()->create();

        if (! $managerOwnsProject) {
            $manager->assignRole('Project Manager');
        }

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'project_manager_id' => $manager->id,
            'status' => 'active',
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'developer',
        ]);

        return [$user, $project];
    }
}