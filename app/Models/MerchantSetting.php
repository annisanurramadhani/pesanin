<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}