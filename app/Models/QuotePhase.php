<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotePhase extends Model
{
    protected $fillable = [
        'quote_id',
        'name',
        'description',
        'estimated_days',
        'deadline_date',
        'sort_order',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
            'subtotal'      => 'decimal:2',
        ];
    }

    // Relationships

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }
}
