<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
        'type',
        'code_hash',
        'is_active',
    ];

    // Relasi ke Model Merchant
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}