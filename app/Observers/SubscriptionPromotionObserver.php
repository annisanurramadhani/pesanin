<?php

namespace App\Observers;

use App\Models\SubscriptionPromotion;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;


class SubscriptionPromotionObserver
{


    /**
     * Ketika promo dibuat
     */
    public function created(SubscriptionPromotion $promotion): void
    {

        $this->log(
            'created',
            $promotion,
            "Menambahkan promo baru: {$promotion->name}"
        );

    }



    /**
     * Ketika promo diperbarui
     */
    public function updated(SubscriptionPromotion $promotion): void
    {

        $this->log(
            'updated',
            $promotion,
            "Mengubah promo: {$promotion->name}"
        );

    }



    /**
     * Ketika promo dihapus
     */
    public function deleted(SubscriptionPromotion $promotion): void
    {

        $this->log(
            'deleted',
            $promotion,
            "Menghapus promo: {$promotion->name}"
        );

    }




    /**
     * Simpan Audit Log
     */
    private function log(
        string $action,
        SubscriptionPromotion $promotion,
        string $description
    ): void {


        AuditLog::create([


            'user_id' => Auth::id(),


            'action' => $action,


            'module' => 'Subscription Promotion',



            'model_type' => SubscriptionPromotion::class,



            'model_id' => $promotion->id,



            'description' => $description,



            'old_values' => $action === 'updated'
                ? $promotion->getOriginal()
                : null,



            'new_values' => in_array(
                $action,
                [
                    'created',
                    'updated'
                ]
            )
                ? $promotion->getAttributes()
                : null,



            'ip_address' => request()->ip(),



            'user_agent' => request()->userAgent(),


        ]);

    }

}
