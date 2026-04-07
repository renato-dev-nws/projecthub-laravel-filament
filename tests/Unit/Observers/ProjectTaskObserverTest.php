<?php

namespace Tests\Unit\Observers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTaskObserverTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $manager = User::factory()->create();
        $client  = Client::factory()->create();

        $this->project = Project::factory()->create([
            'client_id'          => $client->id,
            'project_manager_id' => $manager->id,
            'progress_percent'   => 0,
        ]);
    }

    public function test_progress_is_recalculated_when_task_is_created(): void
    {
        ProjectTask::create([
            'project_id' => $this->project->id,
            'title'      => 'Task A',
            'status'     => 'done',
        ]);

        $this->assertEquals(100, $this->project->fresh()->progress_percent);
    }

    public function test_progress_is_zero_when_no_done_tasks(): void
    {
        ProjectTask::create([
            'project_id' => $this->project->id,
            'title'      => 'Task A',
            'status'     => 'todo',
        ]);

        $this->assertEquals(0, $this->project->fresh()->progress_percent);
    }

    public function test_progress_updates_when_task_status_changes_to_done(): void
    {
        $task = ProjectTask::create([
            'project_id' => $this->project->id,
            'title'      => 'Task A',
            'status'     => 'todo',
        ]);

        $this->assertEquals(0, $this->project->fresh()->progress_percent);

        $task->update(['status' => 'done']);

        $this->assertEquals(100, $this->project->fresh()->progress_percent);
    }

    public function test_progress_does_not_recalculate_when_non_status_field_changes(): void
    {
        $task = ProjectTask::create([
            'project_id' => $this->project->id,
            'title'      => 'Task A',
            'status'     => 'done',
        ]);

        // Force progress to 0 directly to detect unwanted recalculation
        $this->project->update(['progress_percent' => 0]);

        // Changing a non-status field should NOT trigger recalculation
        $task->update(['title' => 'Task A (renamed)']);

        $this->assertEquals(0, $this->project->fresh()->progress_percent);
    }

    public function test_progress_recalculates_with_multiple_tasks(): void
    {
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'done']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'done']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 3', 'status' => 'todo']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 4', 'status' => 'todo']);

        // 2 done / 4 total = 50%
        $this->assertEquals(50, $this->project->fresh()->progress_percent);
    }

    public function test_progress_resets_to_zero_when_last_task_deleted(): void
    {
        $task = ProjectTask::create([
            'project_id' => $this->project->id,
            'title'      => 'Task A',
            'status'     => 'done',
        ]);

        $this->assertEquals(100, $this->project->fresh()->progress_percent);

        $task->delete();

        $this->assertEquals(0, $this->project->fresh()->progress_percent);
    }
}
