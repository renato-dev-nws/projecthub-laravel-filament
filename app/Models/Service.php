<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'unit_type',
        'default_price',
        'is_active',
        'service_category_id',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'type'          => 'string',
            'default_price' => 'decimal:2',
            'is_active'     => 'boolean',
        ];
    }

    // Relationships
    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function pricingTiers(): HasMany
    {
        return $this->hasMany(ServicePricingTier::class)->orderBy('min_hours');
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
