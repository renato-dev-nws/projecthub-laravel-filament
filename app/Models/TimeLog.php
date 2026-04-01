<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLog extends Model
{
    protected $fillable = [
        'project_id',
        'task_id',
        'user_id',
        'description',
        'hours',
        'logged_date',
        'is_billable',
    ];

    protected function casts(): array
    {
        return [
            'hours' => 'decimal:2',
            'logged_date' => 'date',
            'is_billable' => 'boolean',
        ];
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
