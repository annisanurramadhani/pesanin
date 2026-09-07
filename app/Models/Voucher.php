<?php

namespace App\Models;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Voucher milik satu merchant.
     */
    public function merchant()
    {
        return $this->belongsTo(
            Merchant::class
        );
    }

    /**
     * Voucher dapat digunakan pada banyak order.
     */
    public function orders()
    {
        return $this->hasMany(
            Order::class
        );
    }
}