<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientContact extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'email',
        'phone',
        'position',
        'is_primary',
        'can_access_portal',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'can_access_portal' => 'boolean',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
