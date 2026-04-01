<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPhase extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class, 'phase_id');
    }

    public function roadmapItems(): HasMany
    {
        return $this->hasMany(RoadmapItem::class, 'phase_id');
    }
}
