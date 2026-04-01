<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectComment extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (ProjectComment $comment): void {
            if (blank($comment->author_id) && Auth::check()) {
                $comment->author_id = Auth::id();
            }

            if (blank($comment->author_type) && Auth::check()) {
                $comment->author_type = User::class;
            }
        });
    }

    protected $fillable = [
        'project_id',
        'commentable_type',
        'commentable_id',
        'author_type',
        'author_id',
        'content',
        'parent_id',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProjectComment::class, 'parent_id');
    }
}
