<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageDuration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'package_id',
        'name',
        'duration_days',
        'price',
        'discount_price',
        'status',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the package that owns this duration.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the subscriptions that use this package duration.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the effective price.
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Check whether the duration is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
