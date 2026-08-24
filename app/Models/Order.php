<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'qr_code_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'total',
        'payment_method',
        'payment_provider',
        'payment_status',
        'status',
        'receipt_sent_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'receipt_sent_at' => 'datetime',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
