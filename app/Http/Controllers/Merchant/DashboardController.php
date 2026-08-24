<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageDuration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard utama merchant.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $merchantId = $user->merchant_id;


        /*
        |--------------------------------------------------------------------------
        | STATISTIK DASHBOARD
        |--------------------------------------------------------------------------
        */

        $totalMenu = Menu::where(
            'merchant_id',
            $merchantId
        )->count();


        $totalOrders = Order::where(
            'merchant_id',
            $merchantId
        )->count();


        $todayOrders = Order::where(
            'merchant_id',
            $merchantId
        )
            ->whereDate(
                'created_at',
                today()
            )
            ->count();


        $recentOrders = Order::with([
            'qrCode',
            'items.menu'
        ])
            ->where(
                'merchant_id',
                $merchantId
            )
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | SUBSCRIPTION
        |--------------------------------------------------------------------------
        |
        | Yang dianggap aktif hanya subscription:
        |
        | status    = active
        | end_date  >= hari ini
        |
        | Jadi subscription expired lama tidak akan
        | mengganggu subscription baru yang sudah berhasil dibayar.
        |
        */

        $subscription = null;

        $subscriptionExpired = false;


        if ($user->merchant) {

            /*
            |--------------------------------------------------------------------------
            | Cari subscription AKTIF
            |--------------------------------------------------------------------------
            */

            $subscription = $user->merchant
                ->subscriptions()
                ->where(
                    'status',
                    'active'
                )
                ->whereDate(
                    'end_date',
                    '>=',
                    today()
                )
                ->latest('end_date')
                ->latest('id')
                ->first();


            /*
            |--------------------------------------------------------------------------
            | Tidak ada subscription aktif
            |--------------------------------------------------------------------------
            */

            if (!$subscription) {

                $subscriptionExpired = true;


                /*
                |--------------------------------------------------------------------------
                | Ambil subscription terakhir
                |--------------------------------------------------------------------------
                |
                | Hanya digunakan untuk informasi dashboard.
                |
                */

                $subscription = $user->merchant
                    ->subscriptions()
                    ->latest('end_date')
                    ->latest('id')
                    ->first();

            } else {

                /*
                |--------------------------------------------------------------------------
                | SUBSCRIPTION SUDAH AKTIF
                |--------------------------------------------------------------------------
                |
                | Ini bagian penting.
                |
                | Kalau pembayaran baru berhasil:
                |
                | status = active
                |
                | maka semua session renewal lama harus dibersihkan.
                |
                */

                $subscriptionExpired = false;


                session()->forget([
                    'subscription.show_renewal_modal',
                    'subscription.continue_payment',
                    'subscription.from_public',
                    'subscription.package_id',
                    'subscription.duration_id',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SESSION PUBLIC SUBSCRIPTION
        |--------------------------------------------------------------------------
        |
        | Hanya digunakan kalau memang belum memiliki
        | subscription aktif.
        |
        */

        $showRenewalModal = false;

        $renewalPackage = null;

        $renewalDuration = null;


        if ($subscriptionExpired) {

            /*
            |--------------------------------------------------------------------------
            | Cek apakah user sebelumnya memilih paket
            | dari halaman public subscription.
            |--------------------------------------------------------------------------
            */

            $showRenewalModal = session(
                'subscription.show_renewal_modal',
                false
            );


            $packageId = session(
                'subscription.package_id'
            );


            $durationId = session(
                'subscription.duration_id'
            );


            /*
            |--------------------------------------------------------------------------
            | Ambil paket yang sebelumnya dipilih
            |--------------------------------------------------------------------------
            */

            if (
                $packageId &&
                $durationId
            ) {

                $renewalPackage = Package::where(
                    'id',
                    $packageId
                )
                    ->where(
                        'status',
                        'active'
                    )
                    ->first();


                $renewalDuration = PackageDuration::where(
                    'id',
                    $durationId
                )
                    ->where(
                        'package_id',
                        $packageId
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->first();
            }


            /*
            |--------------------------------------------------------------------------
            | LOGIN BIASA + EXPIRED
            |--------------------------------------------------------------------------
            |
            | Kalau user login biasa tanpa membawa pilihan
            | paket dari public subscription, popup harus muncul
            | langsung di dashboard.
            |
            */

            if (!$showRenewalModal) {

                $showRenewalModal = true;

                session([
                    'subscription.show_renewal_modal' => true,
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'merchant.dashboard',
            compact(
                'totalMenu',
                'totalOrders',
                'todayOrders',
                'recentOrders',
                'subscription',
                'subscriptionExpired',
                'showRenewalModal',
                'renewalPackage',
                'renewalDuration'
            )
        );
    }
}
