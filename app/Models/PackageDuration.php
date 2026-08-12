<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageDuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'name',
        'duration_days',
        'price',
        'discount_price',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
