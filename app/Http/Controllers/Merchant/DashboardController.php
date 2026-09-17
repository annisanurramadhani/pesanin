<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItemUnit;
use App\Models\Package;
use App\Models\PackageDuration;
use Illuminate\Http\Request;
use App\Models\MerchantWallet;

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
        | SALDO MERCHANT
        |--------------------------------------------------------------------------
        */

        $wallet = MerchantWallet::firstOrCreate(

            [
                'merchant_id' => $merchantId
            ],

            [
                'balance' => 0
            ]

        );


        /*
        |--------------------------------------------------------------------------
        | TOTAL ORDER
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
        | Hanya:
        | - payment paid
        | - semua menu selesai
        |
        */

        $todayOrders = OrderItemUnit::where(
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
        ->where(
            'orders.payment_status',
            'paid'
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
        */

        $todayRevenue = Order::where(
            'merchant_id',
            $merchantId
        )
        ->where(
            'payment_status',
            'paid'
        )
        ->whereDate(
            'created_at',
            today()
        )
        ->sum('total');



        /*
        |--------------------------------------------------------------------------
        | ORDER TERBARU
        |--------------------------------------------------------------------------
        |
        | Tidak tampil:
        | - expired
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
        ->where(
            'payment_status',
            'paid'
        )
        ->get();


        /*
        |--------------------------------------------------------------------------
        | HITUNG STATUS ORDER
        |--------------------------------------------------------------------------
        */

        $recentOrders->each(function ($order) {


            $orderItemIds =
                $order->items->pluck('id');


            $units =
                OrderItemUnit::whereIn(
                    'order_item_id',
                    $orderItemIds
                )
                ->get();



            $totalUnits =
                $units->count();


            $completedUnits =
                $units
                    ->where(
                        'status',
                        'completed'
                    )
                    ->count();


            $cancelledUnits =
                $units
                    ->where(
                        'status',
                        'cancelled'
                    )
                    ->count();


            $processingUnits =
                $units
                    ->where(
                        'status',
                        'processing'
                    )
                    ->count();


            $pendingUnits =
                $units
                    ->where(
                        'status',
                        'pending'
                    )
                    ->count();



            /*
            |--------------------------------------------------------------------------
            | STATUS DISPLAY
            |--------------------------------------------------------------------------
            */


            if (
                $totalUnits > 0 &&
                $completedUnits == $totalUnits
            ) {

                $order->display_status =
                    'completed';


            } elseif (
                $totalUnits > 0 &&
                $cancelledUnits == $totalUnits
            ) {

                $order->display_status =
                    'cancelled';


            } elseif (
                $cancelledUnits > 0
            ) {

                $order->display_status =
                    'partial_problem';


            } elseif (
                $processingUnits > 0
            ) {

                $order->display_status =
                    'processing';


            } else {

                $order->display_status =
                    'pending';

            }



            $order->status_summary = [

                'total' =>
                    $totalUnits,

                'completed' =>
                    $completedUnits,

                'cancelled' =>
                    $cancelledUnits,

                'processing' =>
                    $processingUnits,

                'pending' =>
                    $pendingUnits,

            ];

        });



        /*
        |--------------------------------------------------------------------------
        | FILTER DASHBOARD
        |--------------------------------------------------------------------------
        |
        | Semua menu bahan habis tidak tampil
        |
        */


        $recentOrders =
            $recentOrders
            ->filter(function ($order) {

                return $order->display_status
                    !== 'cancelled';

            })
            ->sortByDesc(function ($order) {

                return $order->updated_at;

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


            $subscription =
                $user->merchant
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


                $subscription =
                    $user->merchant
                    ->subscriptions()
                    ->latest('end_date')
                    ->latest('id')
                    ->first();

            }

        }





        $showRenewalModal = false;

        $renewalPackage = null;

        $renewalDuration = null;



        if ($subscriptionExpired) {


            $showRenewalModal =
                session(
                    'subscription.show_renewal_modal',
                    false
                );


            $packageId =
                session(
                    'subscription.package_id'
                );


            $durationId =
                session(
                    'subscription.duration_id'
                );



            if (
                $packageId &&
                $durationId
            ) {


                $renewalPackage =
                    Package::where(
                        'id',
                        $packageId
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->first();



                $renewalDuration =
                    PackageDuration::where(
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
                    'subscription.show_renewal_modal'
                    => true
                ]);

            }

        }




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
                'renewalDuration',
                'wallet'
            )
        );

    }
}
