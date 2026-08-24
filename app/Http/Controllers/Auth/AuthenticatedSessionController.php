<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(Request $request): View
    {
        return view('auth.login');
    }


    /**
     * Memproses login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Authenticate User
        |--------------------------------------------------------------------------
        */

        $request->authenticate();

        /*
        |--------------------------------------------------------------------------
        | Regenerate Session
        |--------------------------------------------------------------------------
        |
        | Session lama tetap dipertahankan.
        | Ini penting karena session subscription:
        |
        | subscription.package_id
        | subscription.duration_id
        | subscription.from_public
        |
        | masih dibutuhkan setelah login.
        |
        */

        $request->session()->regenerate();

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | CEK PUBLIC SUBSCRIPTION FLOW
        |--------------------------------------------------------------------------
        |
        | User datang dari:
        |
        | Guest
        | -> Subscription
        | -> Paket
        | -> Durasi
        | -> Ringkasan
        | -> Sudah punya akun
        | -> Login
        |
        | Kalau pilihan paket dan durasi masih ada,
        | JANGAN masuk dashboard.
        |
        | Langsung lanjut ke:
        |
        | /subscription/account/continue
        |
        */

        $packageId = session(
            'subscription.package_id'
        );

        $durationId = session(
            'subscription.duration_id'
        );

        $fromPublicSubscription =
            session(
                'subscription.from_public'
            ) === true;


        $hasSubscriptionSelection =
            !empty($packageId)
            &&
            !empty($durationId)
            &&
            $fromPublicSubscription;


        if ($hasSubscriptionSelection) {

            /*
            |--------------------------------------------------------------------------
            | Pastikan User Memiliki Merchant
            |--------------------------------------------------------------------------
            |
            | Continue payment membutuhkan merchant_id.
            |
            */

            if (!$user->merchant_id) {

                return redirect()
                    ->route('merchant.setup')
                    ->with(
                        'error',
                        'Data toko belum tersedia. Silakan lengkapi data toko terlebih dahulu.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Langsung Lanjut Payment
            |--------------------------------------------------------------------------
            */

            return redirect()->route(
                'public.subscription.account.continue'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN BIASA
        |--------------------------------------------------------------------------
        |
        | Kalau tidak ada session subscription,
        | berarti user memang login secara normal.
        |
        */


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'super_admin') {

            return redirect()->route(
                'super_admin.dashboard'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DAPUR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'dapur') {

            return redirect()->route(
                'merchant.orders.index'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OWNER / KASIR
        |--------------------------------------------------------------------------
        |
        | Dashboard sengaja tidak diberi middleware subscription.active
        | agar DashboardController bisa mendeteksi subscription expired
        | dan menampilkan popup expired langsung saat login.
        |
        */

        return redirect()->route(
            'merchant.dashboard'
        );
    }


    /**
     * Logout user.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {

        Auth::guard('web')->logout();

        /*
        |--------------------------------------------------------------------------
        | Hapus session login
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();

        /*
        |--------------------------------------------------------------------------
        | Regenerate CSRF Token
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
