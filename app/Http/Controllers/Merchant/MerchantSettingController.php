<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
// use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MerchantSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan merchant.
     */
    public function index()
    {
        $merchant = Auth::user()->merchant;

        return view('merchant.settings.index', compact('merchant'));
    }

    /**
     * Memperbarui pengaturan merchant.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $merchant = $user->merchant;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            // 'description' => ['nullable', 'string'],
            'cs_phone' => ['nullable', 'string', 'max:20'],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);


            /*
            |--------------------------------------------------------------------------
            | LOGO MERCHANT - S3 PUBLIC
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')
                    ->store('merchants', 's3_public');

                if (!$logoPath) {
                    throw new \RuntimeException(
                        'Gagal menyimpan logo merchant ke S3.'
                    );
                }

                // Hapus logo lama setelah logo baru berhasil disimpan.
                $oldLogoPath = $merchant->logo;

                $merchant->logo = $logoPath;
                $merchant->save();

                // Hapus file lama dari disk yang sesuai.
                if ($oldLogoPath) {
                    try {
                        Storage::disk('s3_public')->delete($oldLogoPath);
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    try {
                        Storage::disk('public')->delete($oldLogoPath);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE MERCHANT
            |--------------------------------------------------------------------------
            */

            $merchant->name = $validated['name'];
            $merchant->phone = $validated['phone'] ?? null;
            $merchant->address = $validated['address'] ?? null;

            $merchant->save();


            /*
            |--------------------------------------------------------------------------
            | UPDATE MERCHANT SETTINGS
            |--------------------------------------------------------------------------
            */
            $merchant->settings()->updateOrCreate(
                [
                    'merchant_id' => $merchant->id,
                ],
                [
                    'cs_phone' => $validated['cs_phone'] ?? null,
                ]
            );

            // $merchant->settings()->updateOrCreate(
            //     ['merchant_id' => $merchant->id],
            //     [
            //         'description' => $validated['description'] ?? null,
            //     ]
            // );


        return back()->with(
            'success',
            'Pengaturan merchant berhasil diperbarui.'
        );
    }
}
