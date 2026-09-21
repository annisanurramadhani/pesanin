<?php

namespace App\Observers;

use App\Models\Withdrawal;


class WithdrawalObserver
{

    /**
     * Ketika merchant membuat request withdraw
     */
    public function created(Withdrawal $withdrawal): void
    {

        audit(
            'CREATE WITHDRAWAL',
            $withdrawal,
            null,
            $withdrawal->toArray()
        );

    }



    /**
     * Ketika withdraw berubah
     */
    public function updating(Withdrawal $withdrawal): void
    {

        if (!$withdrawal->isDirty()) {
            return;
        }


        $old = $withdrawal->getOriginal();

        $new = $withdrawal->getDirty();



        /*
        |--------------------------------------------------------------------------
        | Audit Status Withdraw
        |--------------------------------------------------------------------------
        */

        if(isset($new['status'])){


            audit(
                'UPDATE WITHDRAWAL STATUS',
                $withdrawal,
                [
                    'status'=>$old['status'] ?? null
                ],
                [
                    'status'=>$new['status']
                ]
            );


            return;

        }



        /*
        |--------------------------------------------------------------------------
        | Audit Payout Status
        |--------------------------------------------------------------------------
        */

        if(isset($new['payout_status'])){


            audit(
                'UPDATE PAYOUT STATUS',
                $withdrawal,
                [
                    'payout_status'=>$old['payout_status'] ?? null
                ],
                [
                    'payout_status'=>$new['payout_status']
                ]
            );


            return;

        }



        /*
        |--------------------------------------------------------------------------
        | Perubahan lainnya
        |--------------------------------------------------------------------------
        */

        audit(
            'UPDATE WITHDRAWAL',
            $withdrawal,
            $old,
            $new
        );

    }




    /**
     * Ketika withdraw dihapus
     */
    public function deleted(Withdrawal $withdrawal): void
    {

        audit(
            'DELETE WITHDRAWAL',
            $withdrawal,
            $withdrawal->toArray(),
            null
        );

    }

}
