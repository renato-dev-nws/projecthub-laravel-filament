<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractClause extends Model
{
    protected $fillable = [
        'title',
        'content',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
