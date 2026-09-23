<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Rankbeam\Seo\Traits\HasSEO;


class WebsiteSetting extends Model
{

    use HasFactory, HasSEO;

    protected $fillable = [

        'website_name',
        'tagline',

        'logo',
        'favicon',


        'hero_badge',
        'hero_content',
        'hero_description',


        'cta_content',
        'cta_description',
        'cta_button_text',


        'footer_text',
        'footer_email',
        'footer_whatsapp',

        'instagram_url',

    ];


    public function getSEOTitle(): ?string
    {
        return 'Solusi Digital untuk Bisnis Kuliner | ' . ($this->website_name ?: 'PesanIn');
    }

    public function getSEODescription(): ?string
    {
        return \Illuminate\Support\Str::limit(
            trim(strip_tags($this->hero_description ?? $this->tagline ?? '')),
            155
        );
    }

    public function getSEOImage(): ?string
    {
        if ($this->logo) {
            return menuImage($this->logo);
        }

        return asset('assets/images/menu-default.jpg');
    }

    public function getUrlForSEO(): string
    {
        return route('home');
    }
}
