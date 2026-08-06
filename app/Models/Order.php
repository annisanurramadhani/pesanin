<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'qr_code_id',
        'order_number',
        'customer_name',
        'total_amount',
        'status',
    ];

    // Relasi ke Merchant
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    // Relasi ke QR Code
    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    // Relasi ke Order Items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}