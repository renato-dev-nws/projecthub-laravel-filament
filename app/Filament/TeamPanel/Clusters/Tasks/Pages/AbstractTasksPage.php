<?php

namespace App\Filament\TeamPanel\Clusters\Tasks\Pages;

use App\Filament\TeamPanel\Clusters\Tasks\TasksCluster;
use App\Models\ProjectTask;
use App\Models\User;
use App\Services\TaskModuleService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

abstract class AbstractTasksPage extends Page
{
    protected static ?string $cluster = TasksCluster::class;

    public ?int $projectId = null;

    public string $scope = 'mine';

    public ?int $memberId = null;

    public bool $isTaskModalOpen = false;

    public ?array $selectedTask = null;

    public array $projectOptions = [];

    public array $memberOptions = [];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && app(TaskModuleService::class)->canAccess($user);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->refreshFilterOptions();
        $this->refreshPageData();
    }

    public function updatedProjectId(): void
    {
        $this->scope = 'mine';
        $this->memberId = null;

        $this->refreshFilterOptions();
        $this->refreshPageData();
    }

    public function updatedScope(): void
    {
        $this->refreshPageData();
    }

    public function updatedMemberId(): void
    {
        $this->refreshPageData();
    }

    public function openTask(int $taskId): void
    {
        $task = $this->taskQuery()->whereKey($taskId)->firstOrFail();

        $this->selectedTask = $this->mapTask($task);
        $this->isTaskModalOpen = true;
    }

    public function closeTask(): void
    {
        $this->isTaskModalOpen = false;
        $this->selectedTask = null;
    }

    public function updateTaskStatus(int $taskId, string $status): void
    {
        if (! array_key_exists($status, ProjectTask::STATUS_LABELS)) {
            return;
        }

        $task = ProjectTask::with(['project', 'phase', 'assignee'])->findOrFail($taskId);

        abort_unless($this->taskPolicy()->view($this->user(), $task), 403);

        if (! $this->taskModule()->canUpdateStatus($this->user(), $task)) {
            Notification::make()
                ->title('Você não pode alterar o status desta tarefa.')
                ->danger()
                ->send();

            return;
        }

        $task->update(['status' => $status]);

        if ($this->selectedTask && Arr::get($this->selectedTask, 'id') === $task->id) {
            $this->selectedTask = $this->mapTask($task->fresh(['project', 'phase', 'assignee']));
        }

        $this->refreshPageData();
    }

    public function moveTask(int $taskId, string $status): void
    {
        $this->updateTaskStatus($taskId, $status);
    }

    public function canSeeTeamScope(): bool
    {
        return $this->taskModule()->canSeeTeamScope($this->user(), $this->projectId);
    }

    public function canFilterMembers(): bool
    {
        return $this->taskModule()->canFilterByMember($this->user());
    }

    public function canChangeStatus(array $task): bool
    {
        $taskModel = ProjectTask::with('project')->find($task['id']);

        return $taskModel ? $this->taskModule()->canUpdateStatus($this->user(), $taskModel) : false;
    }

    public function statusOptions(): array
    {
        return ProjectTask::STATUS_LABELS;
    }

    protected function refreshFilterOptions(): void
    {
        $this->projectOptions = $this->taskModule()->projectOptionsFor($this->user());
        $this->memberOptions = $this->canFilterMembers()
            ? $this->taskModule()->memberOptionsFor($this->user(), $this->projectId)
            : [];
    }

    protected function taskQuery()
    {
        return $this->taskModule()->tasksQuery(
            $this->user(),
            $this->projectId,
            $this->scope,
            $this->memberId,
        );
    }

    protected function mapTask(ProjectTask $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'status_label' => ProjectTask::getStatusLabel($task->status),
            'status_color' => ProjectTask::getStatusColor($task->status),
            'priority' => $task->priority,
            'priority_label' => ProjectTask::getPriorityLabel($task->priority),
            'priority_color' => ProjectTask::getPriorityColor($task->priority),
            'project_id' => $task->project_id,
            'project_code' => $task->project?->code,
            'project_name' => $task->project?->name,
            'phase_name' => $task->phase?->name,
            'assignee_name' => $task->assignee?->name,
            'due_date' => $task->due_date?->format('d/m/Y'),
            'estimated_hours' => $task->estimated_hours,
            'logged_hours' => $task->logged_hours,
        ];
    }

    protected function user(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    protected function taskModule(): TaskModuleService
    {
        return app(TaskModuleService::class);
    }

    protected function taskPolicy(): \App\Policies\ProjectTaskPolicy
    {
        return app(\App\Policies\ProjectTaskPolicy::class);
    }

    abstract protected function refreshPageData(): void;
}