<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
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


        $totalOrders = Order::where(
            'merchant_id',
            $merchantId
        )->count();

        /*
        |--------------------------------------------------------------------------
        | PESANAN HARI INI
        |--------------------------------------------------------------------------
        |
        | Hanya pesanan yang SUDAH SELESAI.
        |
        */
        $todayOrders = Order::where(
            'merchant_id',
            $merchantId
        )
            ->whereDate(
                'created_at',
                today()
            )
            ->where(
                'status',
                'completed'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PENDAPATAN HARI INI
        |--------------------------------------------------------------------------
        |
        | Hanya pesanan:
        | status         = completed
        | payment_status = paid
        |
        */
        $todayRevenueOrders = Order::where(
            'merchant_id',
            $merchantId
        )
            ->whereDate(
                'created_at',
                today()
            )
            ->where(
                'status',
                'completed'
            )
            ->where(
                'payment_status',
                'paid'
            )
            ->get();


        $todayRevenue = $todayRevenueOrders->sum(function ($order) {
            return (float) $order->total;
        });

        /*
        |--------------------------------------------------------------------------
        | PESANAN TERBARU
        |--------------------------------------------------------------------------
        |
        | Expired tidak ditampilkan.
        | Cancelled dari Dapur tetap boleh tampil.
        |
        */
        $recentOrders = Order::with([
            'qrCode',
            'items.menu'
        ])
            ->where(
                'merchant_id',
                $merchantId
            )
            ->where(function ($query) {
                $query->whereNull('payment_status')
                    ->orWhere('payment_status', '!=', 'expired');
            })
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
                'totalOrders',
                'todayOrders',
                'todayRevenue',
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
