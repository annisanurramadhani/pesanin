<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [

        'merchant_id',

        'merchant_bank_account_id',

        'amount',

        'status',

        'payout_id',

        'payout_reference',

        'payout_status',

        'payout_response',

        'approved_by',

        'approved_at',

        'paid_at',

        'payout_attempted_at',

        'note',

    ];

    protected $casts = [

        'approved_at' => 'datetime',

        'paid_at' => 'datetime',

        'payout_attempted_at' => 'datetime',

        'payout_response' => 'array',

    ];

    public function merchant()
    {
        return $this->belongsTo(
            Merchant::class
        );
    }

    public function bankAccount()
    {
        return $this->belongsTo(
            MerchantBankAccount::class,
            'merchant_bank_account_id'
        );
    }

    public function approver()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }
}
