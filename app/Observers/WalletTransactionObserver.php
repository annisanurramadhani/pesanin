<?php

namespace App\Observers;

use App\Models\WalletTransaction;


class WalletTransactionObserver
{

    /**
     * Ketika transaksi wallet dibuat
     */
    public function created(WalletTransaction $transaction): void
    {

        audit(
            'CREATE WALLET TRANSACTION',
            $transaction,
            null,
            [
                'merchant_id' => $transaction->merchant_id,
                'type' => $transaction->type,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'reference_type' => $transaction->reference_type,
                'reference_id' => $transaction->reference_id,
                'description' => $transaction->description,
            ]
        );

    }



    /**
     * Ketika transaksi wallet berubah
     */
    public function updating(WalletTransaction $transaction): void
    {

        if (!$transaction->isDirty()) {
            return;
        }


        $old = $transaction->getOriginal();

        $new = $transaction->getDirty();


        audit(
            'UPDATE WALLET TRANSACTION',
            $transaction,
            $old,
            $new
        );

    }



    /**
     * Ketika transaksi wallet dihapus
     */
    public function deleted(WalletTransaction $transaction): void
    {

        audit(
            'DELETE WALLET TRANSACTION',
            $transaction,
            $transaction->toArray(),
            null
        );

    }

}
