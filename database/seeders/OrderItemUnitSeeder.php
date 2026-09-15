<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\OrderItemUnit;
use Illuminate\Database\Seeder;

class OrderItemUnitSeeder extends Seeder
{
    public function run(): void
    {
        $items = OrderItem::all();

        foreach ($items as $item) {

            for ($i = 1; $i <= $item->quantity; $i++) {

                $status = 'pending';

                /*
                |--------------------------------------------------------------------------
                | Kalau order sudah completed,
                | semua unit dianggap selesai.
                |--------------------------------------------------------------------------
                */

                if ($item->order->status === 'completed') {
                    $status = 'completed';
                }

                /*
                |--------------------------------------------------------------------------
                | Kalau order cancelled,
                | unit dianggap cancelled.
                |--------------------------------------------------------------------------
                */

                if ($item->order->status === 'cancelled') {
                    $status = 'cancelled';
                }

                OrderItemUnit::updateOrCreate(
                    [
                        'order_item_id' => $item->id,
                        'unit_number' => $i,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }
        }

        $this->command->info(
            'Order item unit berhasil dibuat/diperbarui.'
        );
    }
}
