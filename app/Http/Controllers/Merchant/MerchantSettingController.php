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

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

            /*
            |--------------------------------------------------------------------------
            | LOGO
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('logo')) {

                // Hapus logo lama jika ada
                if ($merchant->logo) {
                    Storage::disk('public')->delete($merchant->logo);
                }

                // Simpan logo baru
                $logoPath = $request->file('logo')
                    ->store('merchants', 'public');

                $merchant->logo = $logoPath;
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