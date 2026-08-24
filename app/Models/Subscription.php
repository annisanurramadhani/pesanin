<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'merchant_id',
        'package_duration_id',
        'invoice_number',
        'start_date',
        'end_date',
        'price',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'paid_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function packageDuration()
    {
        return $this->belongsTo(PackageDuration::class);
    }

    /**
     * Menentukan apakah subscription masih aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->end_date
            && !$this->end_date->isPast();
    }

    /**
     * Menentukan apakah subscription sudah expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || (
                $this->end_date
                && $this->end_date->isPast()
            );
    }
}
