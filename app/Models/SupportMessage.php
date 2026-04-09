<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'author_type',
        'author_id',
        'message',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupportMessage $message): void {
            if (blank($message->author_id) && auth('web')->check()) {
                $message->author_type = User::class;
                $message->author_id = auth('web')->id();
            }

            if (blank($message->author_id) && auth('client_portal')->check()) {
                $message->author_type = ClientPortalUser::class;
                $message->author_id = auth('client_portal')->id();
            }
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}
