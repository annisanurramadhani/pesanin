<?php

namespace App\Http\Controllers\PublicSubscription;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPromotion;

class PublicSubscriptionHomeController extends Controller
{
    /**
     * Menampilkan halaman homepage.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil Promo Subscription Aktif
        |--------------------------------------------------------------------------
        |
        | Promo hanya ditampilkan apabila:
        |
        | 1. Status promo active
        | 2. Waktu sekarang sudah melewati starts_at
        | 3. Waktu sekarang belum melewati ends_at
        |
        */

        $activePromotion = SubscriptionPromotion::query()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->latest('created_at')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Homepage
        |--------------------------------------------------------------------------
        */

        return view(
            'public_subscription.homepage',
            compact('activePromotion')
        );
    }
}
