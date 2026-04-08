<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierCategory extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }
}
