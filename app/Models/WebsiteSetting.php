<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{

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

}