<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'quote_phase_id',
        'service_id',
        'description',
        'quantity',
        'hours',
        'unit_price',
        'subtotal',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity'  => 'decimal:2',
            'hours'     => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal'  => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (QuoteItem $quoteItem): void {
            $quoteItem->subtotal = (float) $quoteItem->quantity * (float) $quoteItem->unit_price;
        });

        static::saved(function (QuoteItem $quoteItem): void {
            $quoteItem->quote?->recalculateTotals();
        });

        static::deleted(function (QuoteItem $quoteItem): void {
            $quoteItem->quote?->recalculateTotals();
        });
    }

    // Relationships
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function quotePhase(): BelongsTo
    {
        return $this->belongsTo(QuotePhase::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
