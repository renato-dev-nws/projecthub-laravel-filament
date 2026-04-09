<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProjectDocument extends Model
{
    use SoftDeletes, HasSlug;

    protected static function booted(): void
    {
        static::creating(function (ProjectDocument $document): void {
            if (blank($document->created_by) && auth('web')->check()) {
                $document->created_by = auth('web')->id();
                $document->uploader_type = User::class;
                $document->uploader_id = auth('web')->id();
            }

            if (blank($document->uploader_id) && auth('client_portal')->check()) {
                $document->uploader_type = ClientPortalUser::class;
                $document->uploader_id = auth('client_portal')->id();
            }
        });
    }

    protected $fillable = [
        'project_id',
        'created_by',
        'uploader_type',
        'uploader_id',
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

    public function uploader(): MorphTo
    {
        return $this->morphTo();
    }
}
