<?php

namespace App\Http\Controllers\PublicSubscription;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPromotion;
use App\Models\WebsiteSetting;

class PublicSubscriptionHomeController extends Controller
{
    /**
     * Menampilkan halaman homepage.
     */
    public function index()
    {
        $setting = WebsiteSetting::first();

        /*
        |--------------------------------------------------------------------------
        | Ambil Campaign Diskon Aktif Terbaru
        |--------------------------------------------------------------------------
        |
        | Campaign ditampilkan jika:
        | 1. Status active.
        | 2. Waktu mulai sudah tercapai.
        | 3. Waktu berakhir belum terlewati.
        | 4. Jika ada beberapa campaign, pilih yang terbaru dibuat.
        |
        */

        $activePromotion = SubscriptionPromotion::query()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->latest('created_at')
            ->first();

        return view(
            'public_subscription.homepage',
            compact('activePromotion', 'setting')
        );
    }
}
