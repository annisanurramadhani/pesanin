<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Memastikan merchant memiliki subscription yang masih aktif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Pastikan User Login
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            return redirect()->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan User Memiliki Merchant
        |--------------------------------------------------------------------------
        */

        if (!$user->merchant) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Data merchant tidak ditemukan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cari Subscription Aktif
        |--------------------------------------------------------------------------
        |
        | Subscription dianggap aktif jika:
        |
        | 1. status = active
        | 2. end_date >= hari ini
        |
        */

        $subscription = $user->merchant
            ->subscriptions()
            ->where('status', 'active')
            ->whereDate('end_date', '>=', today())
            ->latest('end_date')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Subscription Tidak Ditemukan
        |--------------------------------------------------------------------------
        */

        if (!$subscription) {
            return redirect()
                ->route('public.subscription.index')
                ->with(
                    'error',
                    'Langganan Anda telah berakhir. Silakan perpanjang langganan untuk menggunakan fitur ini.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Subscription Masih Aktif
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}
