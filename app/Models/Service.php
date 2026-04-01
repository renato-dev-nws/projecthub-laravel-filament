<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'unit_type',
        'default_price',
        'is_active',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'default_price' => 'decimal:2',
            'is_active' => 'boolean',
            'category' => 'string',
        ];
    }

    // Relationships
    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }
}
