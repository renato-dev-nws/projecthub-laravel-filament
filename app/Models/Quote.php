<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    /** @use HasFactory<\Database\Factories\QuoteFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'number',
        'title',
        'description',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'valid_until',
        'notes',
        'terms',
        'created_by',
        'signed_by_name',
        'signed_by_email',
        'signed_token',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'valid_until' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }
}
