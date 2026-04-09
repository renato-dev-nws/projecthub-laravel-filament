<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, SoftDeletes, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'client_id',
        'quote_id',
        'name',
        'code',
        'github_url',
        'description',
        'project_manager_id',
        'status',
        'priority',
        'start_date',
        'end_date',
        'estimated_hours',
        'logged_hours',
        'budget',
        'spent',
        'progress_percent',
        'settings',
        'client_portal_enabled',
        'client_can_comment',
        'color',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'priority' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'logged_hours' => 'integer',
            'budget' => 'decimal:2',
            'spent' => 'decimal:2',
            'progress_percent' => 'integer',
            'settings' => 'array',
            'client_portal_enabled' => 'boolean',
            'client_can_comment' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function projectMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectComment::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function recalculateProgress(): void
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            $this->update(['progress_percent' => 0]);
            return;
        }
        $done = $this->tasks()->where('status', 'done')->count();
        $this->update(['progress_percent' => (int) round(($done / $total) * 100)]);
    }
}
