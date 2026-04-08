<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePricingTier extends Model
{
    protected $fillable = [
        'service_id', 'min_hours', 'max_hours',
        'price_per_hour', 'label', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_hours'      => 'decimal:2',
            'max_hours'      => 'decimal:2',
            'price_per_hour' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
