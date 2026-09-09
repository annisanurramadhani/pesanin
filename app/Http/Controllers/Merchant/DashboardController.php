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
        $todayOrders = \App\Models\OrderItemUnit::where(
    'order_item_units.status',
    'completed'
)
    ->join(
        'order_items',
        'order_item_units.order_item_id',
        '=',
        'order_items.id'
    )
    ->join(
        'orders',
        'order_items.order_id',
        '=',
        'orders.id'
    )
    ->where(
        'orders.merchant_id',
        $merchantId
    )
    ->whereDate(
        'orders.created_at',
        today()
    )
    ->count();

        /*
        |--------------------------------------------------------------------------
        | PENDAPATAN HARI INI
        |--------------------------------------------------------------------------
        |
        | Hanya mengambil pembayaran yang sudah PAID hari ini.
        | Untuk sementara tidak melihat status completed/cancelled.
        |
        */

        $todayRevenue = Order::where(
            'merchant_id',
            $merchantId
        )
            ->whereDate(
                'created_at',
                today()
            )
            ->where(
                'payment_status',
                'paid'
            )
            ->sum('total');

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
            'items.menu',
            'items.unit',
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
| PESANAN TERBARU DASHBOARD
|--------------------------------------------------------------------------
|
| Yang ditampilkan:
| - Selesai
| - Diproses
| - Menunggu
| - Sebagian Bermasalah
|
| Yang TIDAK ditampilkan:
| - Semua menu Bahan Habis
| - Payment expired
|
| Status dihitung berdasarkan OrderItemUnit.
|
*/

$recentOrders = Order::with([
    'qrCode',
    'items.menu',
    'items.unit',
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
    ->get();


/*
|--------------------------------------------------------------------------
| HITUNG STATUS SETIAP ORDER
|--------------------------------------------------------------------------
*/

$recentOrders->each(function ($order) {

    $orderItemIds = $order->items->pluck('id');

    $units = \App\Models\OrderItemUnit::whereIn(
        'order_item_id',
        $orderItemIds
    )->get();


    /*
    |--------------------------------------------------------------------------
    | HITUNG JUMLAH STATUS UNIT
    |--------------------------------------------------------------------------
    */

    $totalUnits = $units->count();

    $completedUnits = $units
        ->where('status', 'completed')
        ->count();

    $cancelledUnits = $units
        ->where('status', 'cancelled')
        ->count();

    $processingUnits = $units
        ->where('status', 'processing')
        ->count();

    $pendingUnits = $units
        ->where('status', 'pending')
        ->count();


    /*
    |--------------------------------------------------------------------------
    | TENTUKAN DISPLAY STATUS
    |--------------------------------------------------------------------------
    */

    if (
        $totalUnits > 0 &&
        $completedUnits === $totalUnits
    ) {

        $order->display_status = 'completed';

    } elseif (
        $totalUnits > 0 &&
        $cancelledUnits === $totalUnits
    ) {

        // Semua menu bahan habis
        $order->display_status = 'cancelled';

    } elseif (
        $cancelledUnits > 0
    ) {

        // Ada sebagian menu yang bahan habis
        $order->display_status = 'partial_problem';

    } elseif (
        $processingUnits > 0
    ) {

        $order->display_status = 'processing';

    } else {

        $order->display_status = 'pending';

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN RINGKASAN STATUS
    |--------------------------------------------------------------------------
    */

    $order->status_summary = [

        'total' => $totalUnits,

        'completed' => $completedUnits,

        'cancelled' => $cancelledUnits,

        'processing' => $processingUnits,

        'pending' => $pendingUnits,

    ];
});


/*
|--------------------------------------------------------------------------
| HANYA TAMPILKAN ORDER YANG DIIZINKAN
|--------------------------------------------------------------------------
|
| Order dengan semua menu cancelled (Bahan Habis)
| tidak ditampilkan di Dashboard.
|
| Sebagian Bermasalah tetap ditampilkan.
|
*/

$recentOrders = $recentOrders
    ->filter(function ($order) {

        return $order->display_status !== 'cancelled';

    })
    ->take(5)
    ->values();
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