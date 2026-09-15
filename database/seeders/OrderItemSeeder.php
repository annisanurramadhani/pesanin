<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {

            $menus = Menu::where(
                'merchant_id',
                $order->merchant_id
            )->get();

            if ($menus->count() < 3) {
                continue;
            }

            $items = [
                [
                    'menu' => $menus->get(0),
                    'quantity' => 2,
                ],
                [
                    'menu' => $menus->get(1),
                    'quantity' => 1,
                ],
            ];

            if ($order->id % 2 === 0 && $menus->count() >= 4) {
                $items[] = [
                    'menu' => $menus->get(3),
                    'quantity' => 2,
                ];
            }

            foreach ($items as $itemData) {

                $menu = $itemData['menu'];
                $quantity = $itemData['quantity'];

                $subtotal = $menu->price * $quantity;

                OrderItem::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'menu_id' => $menu->id,
                    ],
                    [
                        'menu_name' => $menu->name,
                        'price' => $menu->price,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ]
                );
            }
        }

        $this->command->info(
            'Order item berhasil dibuat/diperbarui.'
        );
    }
}
