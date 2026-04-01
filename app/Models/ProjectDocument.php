<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProjectDocument extends Model
{
    use SoftDeletes, HasSlug;

    protected static function booted(): void
    {
        static::creating(function (ProjectDocument $document): void {
            if (blank($document->created_by) && Auth::check()) {
                $document->created_by = Auth::id();
            }
        });
    }

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'slug',
        'content',
        'type',
        'file_path',
        'external_url',
        'is_public',
        'visibility',
        'category',
        'version',
        'version_history',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'is_public' => 'boolean',
            'visibility' => 'string',
            'version_history' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
