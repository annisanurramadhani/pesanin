<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantWallet extends Model
{
    protected $fillable = [
        'merchant_id',
        'balance',
    ];


    protected $casts = [
        'balance' => 'decimal:2',
    ];


    public function merchant()
    {
        return $this->belongsTo(
            Merchant::class
        );
    }


    public function transactions()
    {
        return $this->hasMany(
            WalletTransaction::class,
            'merchant_id',
            'merchant_id'
        );
    }
}
