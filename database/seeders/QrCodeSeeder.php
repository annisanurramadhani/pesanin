<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\QrCode;
use Illuminate\Database\Seeder;

class QrCodeSeeder extends Seeder
{
    public function run(): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {

            $qrCodes = [
                [
                    'name' => 'QR Menu',
                    'type' => 'menu',
                    'code' => strtoupper($merchant->slug) . '-MENU',
                ],
                [
                    'name' => 'Meja 01',
                    'type' => 'dine_in',
                    'code' => strtoupper($merchant->slug) . '-TABLE-01',
                ],
                [
                    'name' => 'Meja 02',
                    'type' => 'dine_in',
                    'code' => strtoupper($merchant->slug) . '-TABLE-02',
                ],
                [
                    'name' => 'QR Takeaway',
                    'type' => 'takeaway',
                    'code' => strtoupper($merchant->slug) . '-TAKEAWAY',
                ],
            ];

            foreach ($qrCodes as $qr) {

                QrCode::updateOrCreate(
                    [
                        'code' => $qr['code'],
                    ],
                    [
                        'merchant_id' => $merchant->id,
                        'name' => $qr['name'],
                        'type' => $qr['type'],
                        'status' => 'active',
                    ]
                );
            }
        }

        $this->command->info(
            'QR Code berhasil dibuat/diperbarui.'
        );
    }
}
