<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'merchant_id',
    'qr_code_id',
    'cashier_id',
    'order_number',
    'customer_name',
    'customer_phone',
    'customer_email',
    'subtotal',
    'total',
    'payment_method',
    'bank',
    'va_number',
    'payment_provider',
    'payment_status',
    'payment_expires_at',
    'status',
    'receipt_sent_at',
    'voucher_id',
    'voucher_code',
    'discount',
];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'receipt_sent_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'discount' => 'decimal:2',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function voucher()
    {
        return $this->belongsTo(
            Voucher::class
        );
    }
}
