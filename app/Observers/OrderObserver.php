<?php

namespace App\Observers;

use App\Models\Order;


class OrderObserver
{

    /**
     * Ketika order dibuat
     */
    public function created(Order $order): void
    {

        audit(
            'CREATE ORDER',
            $order,
            null,
            $order->toArray()
        );
    }



    /**
     * Sebelum order diupdate
     */
    public function updating(Order $order): void
    {

        if (!$order->isDirty()) {
            return;
        }


        $old = $order->getOriginal();

        $new = $order->getDirty();



        /*
    |--------------------------------------------------------------------------
    | Audit Payment Status
    |--------------------------------------------------------------------------
    */

        if (isset($new['payment_status'])) {


            audit(
                'UPDATE PAYMENT STATUS',
                $order,
                [
                    'payment_status' => $old['payment_status'] ?? null
                ],
                [
                    'payment_status' => $new['payment_status']
                ]
            );


            return;
        }



        /*
    |--------------------------------------------------------------------------
    | Audit Order Status
    |--------------------------------------------------------------------------
    */

        if (isset($new['status'])) {


            audit(
                'UPDATE ORDER STATUS',
                $order,
                [
                    'status' => $old['status'] ?? null
                ],
                [
                    'status' => $new['status']
                ]
            );


            return;
        }



        /*
    |--------------------------------------------------------------------------
    | Audit perubahan order lainnya
    |--------------------------------------------------------------------------
    */

        audit(
            'UPDATE ORDER',
            $order,
            $old,
            $new
        );
    }



    /**
     * Ketika order dihapus
     */
    public function deleted(Order $order): void
    {

        audit(
            'DELETE ORDER',
            $order,
            $order->toArray(),
            null
        );
    }
}
