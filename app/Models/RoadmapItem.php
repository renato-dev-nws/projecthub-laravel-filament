<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoadmapItem extends Model
{
    protected $fillable = [
        'project_id',
        'phase_id',
        'title',
        'description',
        'type',
        'status',
        'planned_date',
        'actual_date',
        'is_public',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'status' => 'string',
            'planned_date' => 'date',
            'actual_date' => 'date',
            'is_public' => 'boolean',
        ];
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(ProjectPhase::class, 'phase_id');
    }
}
