<?php

namespace App\Observers;

use App\Models\Menu;


class MenuObserver
{

    /**
     * Ketika menu dibuat
     */
    public function created(Menu $menu): void
    {

        audit(
            'CREATE MENU',
            $menu,
            null,
            $menu->toArray()
        );

    }



    /**
     * Ketika menu berubah
     */
    public function updating(Menu $menu): void
    {

        if (!$menu->isDirty()) {
            return;
        }


        $old = $menu->getOriginal();

        $new = $menu->getDirty();



        /*
        |--------------------------------------------------------------------------
        | Audit perubahan harga
        |--------------------------------------------------------------------------
        */

        if(isset($new['price'])) {


            audit(
                'UPDATE MENU PRICE',
                $menu,
                [
                    'price'=>$old['price'] ?? null
                ],
                [
                    'price'=>$new['price']
                ]
            );


            return;

        }



        /*
        |--------------------------------------------------------------------------
        | Audit status menu
        |--------------------------------------------------------------------------
        */

        if(isset($new['status'])) {


            audit(
                'UPDATE MENU STATUS',
                $menu,
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
        | Update menu lainnya
        |--------------------------------------------------------------------------
        */

        audit(
            'UPDATE MENU',
            $menu,
            $old,
            $new
        );

    }




    /**
     * Ketika menu dihapus
     */
    public function deleted(Menu $menu): void
    {

        audit(
            'DELETE MENU',
            $menu,
            $menu->toArray(),
            null
        );

    }

}
