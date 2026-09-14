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
        'max_qr_codes',
        'max_menus',
        'max_staff',
    ];

    protected $casts = [
        'max_qr_codes' => 'integer',
        'max_menus' => 'integer',
        'max_staff' => 'integer',
    ];

    /**
     * Get the durations for the package.
     */
    public function durations(): HasMany
    {
        return $this->hasMany(PackageDuration::class);
    }
}
