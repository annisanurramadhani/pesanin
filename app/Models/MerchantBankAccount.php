<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MerchantBankAccount extends Model
{


    protected $fillable = [

        'merchant_id',

        'bank_name',

        'account_number',

        'account_name',

        'status',

        'is_locked',

    ];





    protected $casts = [

        'is_locked'=>'boolean',

    ];





    public function merchant()
    {

        return $this->belongsTo(
            Merchant::class
        );

    }


}
