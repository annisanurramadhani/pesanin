<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'badge',
        'status',
        'sort_order',
    ];

    /**
     * Get the durations for the package.
     */
    public function durations(): HasMany
    {
        return $this->hasMany(PackageDuration::class);
    }
}