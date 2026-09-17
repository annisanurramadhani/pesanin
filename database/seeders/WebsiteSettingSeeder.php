<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WebsiteSetting;

class WebsiteSettingSeeder extends Seeder
{
    public function run(): void
    {

        WebsiteSetting::updateOrCreate(
            [
                'id' => 1
            ],
            [

                /*
                |--------------------------------------------------------------------------
                | GENERAL
                |--------------------------------------------------------------------------
                */

                'website_name' => 'PesanIn',

                'tagline' => 'Solusi digital untuk bisnis Anda',



                /*
                |--------------------------------------------------------------------------
                | HERO
                |--------------------------------------------------------------------------
                */

                'hero_badge' => 'Solusi Digital untuk Bisnis Kuliner',

                'hero_content' =>
                'Kelola Bisnis Kuliner Anda dengan Lebih Mudah & Efisien',

                'hero_description' =>
                'PesanIn membantu Anda mengelola menu, pesanan, QR Code, hingga laporan penjualan dalam satu platform yang praktis dan terintegrasi.',



                /*
                |--------------------------------------------------------------------------
                | CTA
                |--------------------------------------------------------------------------
                */

                'cta_content' =>
                'Siap Membuat Bisnis Anda Lebih Mudah?',

                'cta_description' =>
                'Bergabung dengan PesanIn dan nikmati cara yang lebih praktis untuk mengelola bisnis kuliner Anda.',

                'cta_button_text' =>
                'Mulai Sekarang',



                /*
                |--------------------------------------------------------------------------
                | FOOTER
                |--------------------------------------------------------------------------
                */

                'footer_text' =>
                'Semua hak dilindungi.',

                'footer_email' =>
                'support@pesanin.id',

                'footer_whatsapp' =>
                '081234567890',



                /*
                |--------------------------------------------------------------------------
                | SOCIAL MEDIA
                |--------------------------------------------------------------------------
                */

                'instagram_url' =>
                'https://instagram.com/pesanin.id',


            ]
        );

    }
}