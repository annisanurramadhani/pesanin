<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Rankbeam\Seo\Traits\HasSEO;

class Package extends Model
{
    use HasFactory, SoftDeletes, HasSEO;

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

    /*
    |--------------------------------------------------------------------------
    | SEO
    |--------------------------------------------------------------------------
    */

    public function getSEOTitle(): ?string
    {
        return $this->name
            ? $this->name . ' | Paket Langganan PesanIn'
            : 'Paket Langganan PesanIn';
    }

    public function getSEODescription(): ?string
    {
        $description = $this->description
            ? strip_tags($this->description)
            : 'Paket langganan PesanIn untuk membantu bisnis mengelola menu, pesanan, QR Code, dan operasional bisnis.';

        return Str::limit(
            trim($description),
            155
        );
    }

    public function getSEOImage(): ?string
    {
        return config('seo.default_og_image');
    }

    public function getUrlForSEO(): string
    {
        return route('public.subscription.show', [
            'slug' => $this->slug,
        ]);
    }
}