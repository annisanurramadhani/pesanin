<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Rules\SecureText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteSettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan website.
     */
    public function index()
    {
        $setting = WebsiteSetting::first();

        return view(
            'super_admin.settings.index',
            compact('setting')
        );
    }

    /**
     * Memperbarui pengaturan website.
     */
    public function update(Request $request)
    {
        $setting = WebsiteSetting::first();

        if (!$setting) {
            return back()->with(
                'error',
                'Pengaturan website belum tersedia.'
            );
        }

        $validated = $request->validate([
            /*
            |--------------------------------------------------------------------------
            | GENERAL
            |--------------------------------------------------------------------------
            */
            'website_name' => [
                'required',
                'string',
                'max:255',
                new SecureText(),
            ],

            'tagline' => [
                'nullable',
                'string',
                'max:255',
                new SecureText(),
            ],

            /*
            |--------------------------------------------------------------------------
            | LOGO & FAVICON
            |--------------------------------------------------------------------------
            */
            'logo' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:2048',
            ],

            'favicon' => [
                'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:2048',
            ],

            /*
            |--------------------------------------------------------------------------
            | HERO
            |--------------------------------------------------------------------------
            */
            'hero_badge' => [
                'nullable',
                'string',
                'max:255',
            ],

            'hero_content' => [
                'required',
                'string',
                new SecureText(),
            ],

            'hero_description' => [
                'required',
                'string',
                new SecureText(),
            ],

            /*
            |--------------------------------------------------------------------------
            | CTA
            |--------------------------------------------------------------------------
            */
            'cta_content' => [
                'required',
                'string',
                new SecureText(),
            ],

            'cta_description' => [
                'nullable',
                'string',
                new SecureText(),
            ],

            'cta_button_text' => [
                'required',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | FOOTER
            |--------------------------------------------------------------------------
            */
            'footer_text' => [
                'nullable',
                'string',
                'max:255',
                new SecureText(),
            ],

            'footer_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'footer_whatsapp' => [
                'nullable',
                'string',
                'max:20',
                new SecureText(),
            ],

            /*
            |--------------------------------------------------------------------------
            | SOCIAL MEDIA
            |--------------------------------------------------------------------------
            */
            'instagram_url' => [
                'nullable',
                'url',
                'max:255',
            ],
        ]);

        $oldFiles = [];
        $newFiles = [];

        /*
        |--------------------------------------------------------------------------
        | UPLOAD LOGO - S3 PUBLIC
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')
                ->store('website', 's3_public');

            if (!$logoPath) {
                throw new \RuntimeException(
                    'Gagal menyimpan logo website ke S3.'
                );
            }

            $validated['logo'] = $logoPath;
            $newFiles[] = $logoPath;

            if ($setting->logo) {
                $oldFiles[] = $setting->logo;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOAD FAVICON - S3 PUBLIC
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')
                ->store('website', 's3_public');

            if (!$faviconPath) {
                // Hapus logo baru jika upload favicon gagal.
                foreach ($newFiles as $path) {
                    try {
                        Storage::disk('s3_public')->delete($path);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }

                throw new \RuntimeException(
                    'Gagal menyimpan favicon website ke S3.'
                );
            }

            $validated['favicon'] = $faviconPath;
            $newFiles[] = $faviconPath;

            if ($setting->favicon) {
                $oldFiles[] = $setting->favicon;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATABASE
        |--------------------------------------------------------------------------
        */
        try {
            $setting->update($validated);
        } catch (\Throwable $e) {
            // Bersihkan file baru jika update database gagal.
            foreach ($newFiles as $path) {
                try {
                    Storage::disk('s3_public')->delete($path);
                } catch (\Throwable $deleteError) {
                    report($deleteError);
                }
            }

            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | HAPUS FILE LAMA SETELAH UPDATE BERHASIL
        |--------------------------------------------------------------------------
        */
        foreach ($oldFiles as $path) {
            // Hapus dari S3 jika file lama berasal dari S3.
            try {
                Storage::disk('s3_public')->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }

            // Bersihkan file lokal lama jika sebelumnya tersimpan lokal.
            try {
                Storage::disk('public')->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with(
            'success',
            'Pengaturan website berhasil diperbarui.'
        );
    }
}
