<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'project_id',
        'client_id',
        'requester_type',
        'requester_id',
        'subject',
        'description',
        'status',
        'priority',
        'assigned_to',
        'project_manager_id',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'priority' => 'string',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket): void {
            if (blank($ticket->code)) {
                $ticket->code = 'SUP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
            }

            if (blank($ticket->requester_id) && auth('web')->check()) {
                $ticket->requester_type = User::class;
                $ticket->requester_id = auth('web')->id();
            }

            if (blank($ticket->requester_id) && auth('client_portal')->check()) {
                $ticket->requester_type = ClientPortalUser::class;
                $ticket->requester_id = auth('client_portal')->id();
            }

            if (blank($ticket->project_manager_id) && $ticket->project) {
                $ticket->project_manager_id = $ticket->project->project_manager_id;
            }

            if (blank($ticket->client_id) && $ticket->project) {
                $ticket->client_id = $ticket->project->client_id;
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function requester(): MorphTo
    {
        return $this->morphTo();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->latest();
    }
}
