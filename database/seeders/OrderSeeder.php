<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\QrCode;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            // Relasi
            $cashier = User::where('merchant_id', $merchant->id)
                ->where('role', 'kasir')
                ->where('status', 'active')
                ->first();

            $dineIn = QrCode::where('merchant_id', $merchant->id)
                ->where('type', 'dine_in')
                ->first();

            $takeaway = QrCode::where('merchant_id', $merchant->id)
                ->where('type', 'takeaway')
                ->first();

            $voucher = Voucher::where('merchant_id', $merchant->id)
                ->where('code', 'HEMAT10')
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Order 1 - Completed / Paid / Cash
            |--------------------------------------------------------------------------
            */

            Order::updateOrCreate(
                [
                    'order_number' => 'ORD-202609150001-A1B2C3',
                ],
                [
                    'merchant_id' => $merchant->id,
                    'qr_code_id' => $dineIn?->id,
                    'cashier_id' => $cashier?->id,

                    'customer_name' => 'Budi',
                    'customer_phone' => '081234567801',
                    'customer_email' => 'budi@example.test',

                    'subtotal' => 41000,
                    'discount' => 0,
                    'total' => 41000,

                    'payment_method' => 'cash',
                    'bank' => null,
                    'va_number' => null,
                    'payment_provider' => null,
                    'payment_status' => 'paid',
                    'payment_expires_at' => null,

                    'status' => 'completed',
                    'receipt_sent_at' => now(),

                    'voucher_id' => null,
                    'voucher_code' => null,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Order 2 - Processing / Paid / QRIS
            |--------------------------------------------------------------------------
            */

            Order::updateOrCreate(
                [
                    'order_number' => 'ORD-202609150002-D4E5F6',
                ],
                [
                    'merchant_id' => $merchant->id,
                    'qr_code_id' => $dineIn?->id,
                    'cashier_id' => $cashier?->id,

                    'customer_name' => 'Siti',
                    'customer_phone' => '081234567802',
                    'customer_email' => 'siti@example.test',

                    'subtotal' => 36000,
                    'discount' => 0,
                    'total' => 36000,

                    'payment_method' => 'qris',
                    'bank' => null,
                    'va_number' => null,
                    'payment_provider' => 'midtrans',
                    'payment_status' => 'paid',
                    'payment_expires_at' => now()->addMinutes(15),

                    'status' => 'processing',
                    'receipt_sent_at' => null,

                    'voucher_id' => null,
                    'voucher_code' => null,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Order 3 - Pending / Cash
            |--------------------------------------------------------------------------
            */

            Order::updateOrCreate(
                [
                    'order_number' => 'ORD-202609150003-G7H8I9',
                ],
                [
                    'merchant_id' => $merchant->id,
                    'qr_code_id' => $takeaway?->id,
                    'cashier_id' => null,

                    'customer_name' => 'Andi',
                    'customer_phone' => '081234567803',
                    'customer_email' => null,

                    'subtotal' => 25000,
                    'discount' => 0,
                    'total' => 25000,

                    'payment_method' => 'cash',
                    'bank' => null,
                    'va_number' => null,
                    'payment_provider' => null,
                    'payment_status' => 'pending',
                    'payment_expires_at' => null,

                    'status' => 'pending',
                    'receipt_sent_at' => null,

                    'voucher_id' => null,
                    'voucher_code' => null,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Order 4 - Completed / Paid / Voucher
            |--------------------------------------------------------------------------
            */

            if ($voucher) {
                Order::updateOrCreate(
                    [
                        'order_number' => 'ORD-202609150004-J1K2L3',
                    ],
                    [
                        'merchant_id' => $merchant->id,
                        'qr_code_id' => $dineIn?->id,
                        'cashier_id' => $cashier?->id,

                        'customer_name' => 'Rina',
                        'customer_phone' => '081234567804',
                        'customer_email' => 'rina@example.test',

                        'subtotal' => 50000,
                        'discount' => 5000,
                        'total' => 45000,

                        'payment_method' => 'cash',
                        'bank' => null,
                        'va_number' => null,
                        'payment_provider' => null,
                        'payment_status' => 'paid',
                        'payment_expires_at' => null,

                        'status' => 'completed',
                        'receipt_sent_at' => now(),

                        'voucher_id' => $voucher->id,
                        'voucher_code' => $voucher->code,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Order 5 - Expired / QRIS
            |--------------------------------------------------------------------------
            */

            Order::updateOrCreate(
                [
                    'order_number' => 'ORD-202609150005-M4N5O6',
                ],
                [
                    'merchant_id' => $merchant->id,
                    'qr_code_id' => $dineIn?->id,
                    'cashier_id' => null,

                    'customer_name' => 'Doni',
                    'customer_phone' => '081234567805',
                    'customer_email' => null,

                    'subtotal' => 30000,
                    'discount' => 0,
                    'total' => 30000,

                    'payment_method' => 'qris',
                    'bank' => null,
                    'va_number' => null,
                    'payment_provider' => 'midtrans',
                    'payment_status' => 'expired',
                    'payment_expires_at' => now()->subMinutes(30),

                    'status' => 'pending',
                    'receipt_sent_at' => null,

                    'voucher_id' => null,
                    'voucher_code' => null,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Order 6 - Cancelled / Failed
            |--------------------------------------------------------------------------
            */

            Order::updateOrCreate(
                [
                    'order_number' => 'ORD-202609150006-P7Q8R9',
                ],
                [
                    'merchant_id' => $merchant->id,
                    'qr_code_id' => $takeaway?->id,
                    'cashier_id' => null,

                    'customer_name' => 'Rudi',
                    'customer_phone' => '081234567806',
                    'customer_email' => null,

                    'subtotal' => 20000,
                    'discount' => 0,
                    'total' => 20000,

                    'payment_method' => 'cash',
                    'bank' => null,
                    'va_number' => null,
                    'payment_provider' => null,
                    'payment_status' => 'failed',
                    'payment_expires_at' => null,

                    'status' => 'cancelled',
                    'receipt_sent_at' => null,

                    'voucher_id' => null,
                    'voucher_code' => null,
                ]
            );
        }

        $this->command->info(
            'Order berhasil dibuat/diperbarui.'
        );
    }
}
