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
        // TAMBAHKAN BARIS INI: Menghitung total pendapatan keseluruhan (semua order yang sukses/paid)
        $totalRevenue = Order::where(
            'merchant_id',
            $merchantId
        )
            ->where('payment_status', 'paid') // Sesuaikan jika status lunas di database kamu berbeda
            ->sum('total');


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
        */

        $subscription = null;

        $subscriptionExpired = false;


        if ($user->merchant) {

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


            if (!$subscription) {

                $subscriptionExpired = true;

                $subscription = $user->merchant
                    ->subscriptions()
                    ->latest('end_date')
                    ->latest('id')
                    ->first();

            } else {

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


        $showRenewalModal = false;

        $renewalPackage = null;

        $renewalDuration = null;


        if ($subscriptionExpired) {

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
                'totalRevenue', // Masukkan variabel ini ke compact
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