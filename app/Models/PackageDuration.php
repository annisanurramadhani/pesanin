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
     * Promo yang berlaku untuk durasi paket ini.
     */
    public function promotions()
    {
        return $this->belongsToMany(
            SubscriptionPromotion::class,
            'subscription_promotion_durations',
            'package_duration_id',
            'promotion_id'
        )->withTimestamps();
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


    public function getSubscriptionPrice(): array
{
    /*
    |--------------------------------------------------------------------------
    | Harga normal
    |--------------------------------------------------------------------------
    */
    $normalPrice = (float) $this->price;

    /*
    |--------------------------------------------------------------------------
    | Harga discount bawaan duration
    |--------------------------------------------------------------------------
    */
    $discountPrice = !is_null($this->discount_price)
        ? (float) $this->discount_price
        : null;

    /*
    |--------------------------------------------------------------------------
    | Default harga final
    |--------------------------------------------------------------------------
    |
    | Kalau ada discount_price, gunakan sebagai harga awal.
    |
    */
    $finalPrice = $discountPrice ?? $normalPrice;

    $promotion = null;

    /*
    |--------------------------------------------------------------------------
    | Ambil semua promotion yang sedang aktif
    |--------------------------------------------------------------------------
    */
    $activePromotions = $this->promotions()
        ->where('subscription_promotions.status', 'active')
        ->where(
            'subscription_promotions.starts_at',
            '<=',
            now()
        )
        ->where(
            'subscription_promotions.ends_at',
            '>=',
            now()
        )
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Cari promotion dengan harga paling murah
    |--------------------------------------------------------------------------
    */
    foreach ($activePromotions as $activePromotion) {

        if ($activePromotion->discount_type === 'percentage') {

            $promotionPrice = $normalPrice
                - (
                    $normalPrice
                    * (
                        (float) $activePromotion->discount_value
                        / 100
                    )
                );

        } else {

            $promotionPrice = $normalPrice
                - (float) $activePromotion->discount_value;
        }

        /*
        |--------------------------------------------------------------------------
        | Jangan sampai harga negatif
        |--------------------------------------------------------------------------
        */
        $promotionPrice = max(
            0,
            $promotionPrice
        );

        /*
        |--------------------------------------------------------------------------
        | Kalau promotion lebih murah dari harga saat ini,
        | jadikan sebagai kandidat harga terbaik.
        |--------------------------------------------------------------------------
        */
        if ($promotionPrice < $finalPrice) {

            $finalPrice = $promotionPrice;

            $promotion = $activePromotion;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Return hasil perhitungan
    |--------------------------------------------------------------------------
    */
    return [
        'normal_price' => $normalPrice,

        'discount_price' => $discountPrice,

        'final_price' => round(
            $finalPrice,
            2
        ),

        'promotion' => $promotion,
    ];
}
}
