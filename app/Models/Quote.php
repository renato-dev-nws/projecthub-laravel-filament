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
        'lead_id',
        'number',
        'title',
        'description',
        'status',
        'subtotal',
        'discount_percent',
        'discount_value',
        'tax_percent',
        'tax_value',
        'total',
        'currency',
        'valid_until',
        'terms_conditions',
        'internal_notes',
        'created_by',
        'signed_by_name',
        'signed_by_email',
        'signed_token',
        'sent_at',
        'viewed_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'subtotal' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_value' => 'decimal:2',
            'total' => 'decimal:2',
            'valid_until' => 'date',
            'sent_at' => 'datetime',
            'viewed_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->sum('subtotal');
        $discountValue = (float) ($this->discount_value ?? 0);
        $taxValue = (float) ($this->tax_value ?? 0);

        $this->forceFill([
            'subtotal' => $subtotal,
            'total' => max($subtotal - $discountValue + $taxValue, 0),
        ])->saveQuietly();
    }
}
