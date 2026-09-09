<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPromotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Durasi paket yang mendapatkan promo ini.
     */
    public function durations()
    {
        return $this->belongsToMany(
            PackageDuration::class,
            'subscription_promotion_durations',
            'promotion_id',
            'package_duration_id'
        )->withTimestamps();
    }

    /**
     * Subscription yang menggunakan promo ini.
     */
    public function subscriptions()
    {
        return $this->hasMany(
            Subscription::class,
            'promotion_id'
        );
    }

    /**
     * Mengecek apakah promo sedang aktif berdasarkan
     * status dan periode waktunya.
     */
    public function isCurrentlyActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->starts_at, $this->ends_at);
    }
}
