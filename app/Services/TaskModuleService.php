<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TaskModuleService
{
    public function canAccess(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager', 'Developer', 'Designer']);
    }

    public function projectOptionsFor(User $user): array
    {
        return $this->visibleProjectsFor($user)
            ->mapWithKeys(fn (Project $project) => [
                $project->id => "{$project->code} - {$project->name}",
            ])
            ->all();
    }

    public function visibleProjectsFor(User $user): Collection
    {
        $query = Project::query()
            ->withCount([
                'tasks as open_tasks_count' => fn (Builder $builder) => $builder->whereIn('status', ProjectTask::OPEN_STATUSES),
                'tasks as my_open_tasks_count' => fn (Builder $builder) => $builder
                    ->whereIn('status', ProjectTask::OPEN_STATUSES)
                    ->where('assigned_to', $user->id),
            ]);

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $projects = $query->get();
        } elseif ($user->hasRole('Project Manager')) {
            $projects = $query->where('project_manager_id', $user->id)->get();
        } else {
            $projects = $query
                ->whereHas('members', fn (Builder $builder) => $builder->where('user_id', $user->id))
                ->get();
        }

        return $projects->sort(function (Project $left, Project $right) use ($user): int {
            $leftPriority = $this->projectPriorityTuple($left, $user);
            $rightPriority = $this->projectPriorityTuple($right, $user);

            return $rightPriority <=> $leftPriority ?: strcasecmp($left->name, $right->name);
        })->values();
    }

    public function memberOptionsFor(User $user, ?int $projectId = null): array
    {
        $memberQuery = User::query()
            ->whereHas('assignedTasks', fn (Builder $builder) => $this->applyVisibilityConstraints($builder, $user, $projectId));

        if ($projectId) {
            $memberQuery->orWhereHas('projectMemberships', fn (Builder $builder) => $builder->where('project_id', $projectId));
        }

        return $memberQuery
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function canSeeTeamScope(User $user, ?int $projectId): bool
    {
        if (! $projectId) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        $project = Project::find($projectId);

        if (! $project) {
            return false;
        }

        if ($user->hasRole('Project Manager')) {
            return $project->project_manager_id === $user->id;
        }

        return $project->members()->where('user_id', $user->id)->exists();
    }

    public function canFilterByMember(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Project Manager']);
    }

    public function canUpdateStatus(User $user, ProjectTask $task): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if ($user->hasRole('Project Manager')) {
            return $task->project?->project_manager_id === $user->id;
        }

        return $task->assigned_to === $user->id;
    }

    public function tasksQuery(
        User $user,
        ?int $projectId = null,
        string $scope = 'mine',
        ?int $memberId = null,
    ): Builder {
        $query = ProjectTask::query()
            ->with(['project', 'phase', 'assignee'])
            ->whereHas('project', fn (Builder $builder) => $this->applyProjectVisibility($builder, $user));

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        if ($memberId && $this->canFilterByMember($user)) {
            $query->where('assigned_to', $memberId);
        } else {
            $this->applyScopeFilters($query, $user, $projectId, $scope);
        }

        return $query
            ->orderByRaw("case when status in ('todo', 'in_progress', 'review', 'blocked') then 0 else 1 end")
            ->orderBy('due_date')
            ->orderBy('title');
    }

    private function applyVisibilityConstraints(Builder $query, User $user, ?int $projectId = null): Builder
    {
        $query->whereHas('project', fn (Builder $builder) => $this->applyProjectVisibility($builder, $user));

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }

    private function applyProjectVisibility(Builder $query, User $user): Builder
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return $query;
        }

        if ($user->hasRole('Project Manager')) {
            return $query->where('project_manager_id', $user->id);
        }

        return $query->whereHas('members', fn (Builder $builder) => $builder->where('user_id', $user->id));
    }

    private function applyScopeFilters(Builder $query, User $user, ?int $projectId, string $scope): void
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            if ($projectId && $scope === 'mine') {
                $query->where('assigned_to', $user->id);
            }

            return;
        }

        if ($user->hasRole('Project Manager')) {
            if ($projectId && $scope === 'mine') {
                $query->where('assigned_to', $user->id);
            }

            return;
        }

        if (! $projectId || $scope === 'mine' || ! $this->canSeeTeamScope($user, $projectId)) {
            $query->where('assigned_to', $user->id);
        }
    }

    private function projectPriorityTuple(Project $project, User $user): array
    {
        $isActive = $project->status === 'active' ? 1 : 0;
        $myOpenTasks = (int) ($project->my_open_tasks_count ?? 0);
        $openTasks = (int) ($project->open_tasks_count ?? 0);

        if ($user->hasAnyRole(['Developer', 'Designer'])) {
            return [$myOpenTasks > 0 ? 1 : 0, $isActive, $myOpenTasks, $openTasks];
        }

        return [$isActive, $openTasks > 0 ? 1 : 0, $openTasks, $myOpenTasks];
    }
}