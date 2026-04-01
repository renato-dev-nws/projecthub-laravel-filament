<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'website',
        'source',
        'status',
        'priority',
        'estimated_value',
        'description',
        'expected_close_date',
        'assigned_to',
        'converted_client_id',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'priority' => 'string',
            'estimated_value' => 'decimal:2',
            'expected_close_date' => 'date',
            'converted_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    // Relationships
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function convertedClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'converted_client_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }
}
