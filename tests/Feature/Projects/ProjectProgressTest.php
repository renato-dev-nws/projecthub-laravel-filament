<?php

namespace Tests\Feature\Projects;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectProgressTest extends TestCase
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
        ]);
    }

    public function test_new_project_starts_at_zero_percent(): void
    {
        $this->assertEquals(0, $this->project->progress_percent);
    }

    public function test_progress_is_zero_when_all_tasks_are_pending(): void
    {
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'todo']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'in_progress']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 3', 'status' => 'review']);

        $this->assertEquals(0, $this->project->fresh()->progress_percent);
    }

    public function test_progress_is_100_when_all_tasks_are_done(): void
    {
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'done']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'done']);

        $this->assertEquals(100, $this->project->fresh()->progress_percent);
    }

    public function test_progress_is_proportional_to_done_tasks(): void
    {
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'done']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'done']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 3', 'status' => 'todo']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 4', 'status' => 'todo']);

        $this->assertEquals(50, $this->project->fresh()->progress_percent);
    }

    public function test_completing_a_task_increases_progress(): void
    {
        $task1 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'todo']);
        $task2 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'todo']);

        $this->assertEquals(0, $this->project->fresh()->progress_percent);

        $task1->update(['status' => 'done']);
        $this->assertEquals(50, $this->project->fresh()->progress_percent);

        $task2->update(['status' => 'done']);
        $this->assertEquals(100, $this->project->fresh()->progress_percent);
    }

    public function test_reverting_done_task_decreases_progress(): void
    {
        $task = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'done']);
        ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'done']);

        $this->assertEquals(100, $this->project->fresh()->progress_percent);

        $task->update(['status' => 'in_progress']);
        $this->assertEquals(50, $this->project->fresh()->progress_percent);
    }

    public function test_deleting_all_tasks_resets_progress_to_zero(): void
    {
        $task1 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'done']);
        $task2 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'done']);

        $this->assertEquals(100, $this->project->fresh()->progress_percent);

        $task1->delete();
        $task2->delete();

        $this->assertEquals(0, $this->project->fresh()->progress_percent);
    }

    public function test_soft_deleted_tasks_are_excluded_from_progress(): void
    {
        $task1 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 1', 'status' => 'done']);
        $task2 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 2', 'status' => 'done']);
        $task3 = ProjectTask::create(['project_id' => $this->project->id, 'title' => 'Task 3', 'status' => 'todo']);

        // 2 done / 3 total ≈ 67%
        $this->assertEquals(67, $this->project->fresh()->progress_percent);

        // Soft-delete the todo task
        $task3->delete();

        // Now 2 done / 2 total = 100%
        $this->assertEquals(100, $this->project->fresh()->progress_percent);
    }
}
