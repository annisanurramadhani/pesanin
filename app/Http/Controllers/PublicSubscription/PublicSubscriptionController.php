<?php

namespace App\Http\Controllers\PublicSubscription;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDuration;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class PublicSubscriptionController extends Controller
{
    /**
     * Halaman utama public subscription.
     */
    public function index()
    {
        $packages = Package::where(
            'status',
            'active'
        )
            ->orderBy('id')
            ->get();

        return view(
            'public_subscription.index',
            compact('packages')
        );
    }


    /**
     * Menampilkan pilihan durasi berdasarkan paket.
     */
    public function show(string $slug)
    {
        $package = Package::where(
            'slug',
            $slug
        )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        $durations = PackageDuration::where(
            'package_id',
            $package->id
        )
            ->where(
                'status',
                'active'
            )
            ->orderBy(
                'duration_days'
            )
            ->get();


        return view(
            'public_subscription.duration',
            compact(
                'package',
                'durations'
            )
        );
    }


    /**
     * Menampilkan ringkasan pesanan.
     */
    public function summary(
        string $slug,
        string $encryptedDuration
    ) {

        /*
        |--------------------------------------------------------------------------
        | Ambil Package
        |--------------------------------------------------------------------------
        */

        $package = Package::where(
            'slug',
            $slug
        )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Dekripsi Duration ID
        |--------------------------------------------------------------------------
        */

        $durationId = decryptId(
            $encryptedDuration
        );


        if (!$durationId) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Duration
        |--------------------------------------------------------------------------
        */

        $duration = PackageDuration::where(
            'id',
            $durationId
        )
            ->where(
                'package_id',
                $package->id
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Harga
        |--------------------------------------------------------------------------
        */

        $price =
            $duration->discount_price
            ?? $duration->price;


        $hasDiscount =
            !is_null(
                $duration->discount_price
            )
            &&
            $duration->discount_price
            <
            $duration->price;


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PILIHAN KE SESSION
        |--------------------------------------------------------------------------
        |
        | Session ini harus tetap ada ketika user:
        |
        | Guest
        | -> pilih paket
        | -> pilih durasi
        | -> ringkasan
        | -> sudah punya akun
        | -> login
        |
        */

        session([
            'subscription.package_id' =>
                $package->id,

            'subscription.duration_id' =>
                $duration->id,

            'subscription.from_public' =>
                true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Tampilkan Summary
        |--------------------------------------------------------------------------
        */

        return view(
            'public_subscription.summary',
            compact(
                'package',
                'duration',
                'price',
                'hasDiscount'
            )
        );
    }


    /**
     * Memilih akun.
     */
    public function account(
        string $encryptedDuration
    ) {

        /*
        |--------------------------------------------------------------------------
        | Dekripsi Duration
        |--------------------------------------------------------------------------
        */

        $durationId = decryptId(
            $encryptedDuration
        );


        if (!$durationId) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Duration
        |--------------------------------------------------------------------------
        */

        $duration = PackageDuration::with(
            'package'
        )
            ->where(
                'id',
                $durationId
            )
            ->where(
                'status',
                'active'
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PILIHAN
        |--------------------------------------------------------------------------
        |
        | Jangan hapus session di halaman ini.
        |
        | Session harus bertahan sampai login selesai.
        |
        */

        session([
            'subscription.package_id' =>
                $duration->package_id,

            'subscription.duration_id' =>
                $duration->id,

            'subscription.from_public' =>
                true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Kalau User Sudah Login
        |--------------------------------------------------------------------------
        |
        | Tidak perlu menampilkan halaman login/account lagi.
        | Langsung lanjut membuat subscription pending.
        |
        */

        if (Auth::check()) {

            return redirect()->route(
                'public.subscription.account.continue'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | User Belum Login
        |--------------------------------------------------------------------------
        |
        | Tampilkan halaman:
        |
        | "Sudah punya akun?"
        |
        */

        return view(
            'public_subscription.account',
            compact('duration')
        );
    }


    /**
     * Melanjutkan subscription setelah user login.
     */
    public function continuePayment()
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan Login
        |--------------------------------------------------------------------------
        */

        if (!Auth::check()) {

            return redirect()
                ->route('login');
        }


        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Ambil Session Pilihan
        |--------------------------------------------------------------------------
        */

        $packageId = session(
            'subscription.package_id'
        );


        $durationId = session(
            'subscription.duration_id'
        );


        /*
        |--------------------------------------------------------------------------
        | Validasi Session
        |--------------------------------------------------------------------------
        */

        if (
            empty($packageId)
            ||
            empty($durationId)
        ) {

            return redirect()
                ->route(
                    'public.subscription.index'
                )
                ->with(
                    'error',
                    'Pilihan paket dan durasi sebelumnya tidak ditemukan. Silakan pilih paket kembali.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan Merchant Ada
        |--------------------------------------------------------------------------
        */

        if (!$user->merchant_id) {

            return redirect()
                ->route(
                    'merchant.setup'
                )
                ->with(
                    'error',
                    'Data toko belum tersedia. Silakan lengkapi data toko terlebih dahulu.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Package
        |--------------------------------------------------------------------------
        */

        $package = Package::where(
            'id',
            $packageId
        )
            ->where(
                'status',
                'active'
            )
            ->first();


        if (!$package) {

            session()->forget(
                'subscription'
            );


            return redirect()
                ->route(
                    'public.subscription.index'
                )
                ->with(
                    'error',
                    'Paket yang dipilih sudah tidak tersedia. Silakan pilih paket kembali.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil Duration
        |--------------------------------------------------------------------------
        */

        $duration = PackageDuration::where(
            'id',
            $durationId
        )
            ->where(
                'package_id',
                $package->id
            )
            ->where(
                'status',
                'active'
            )
            ->first();


        if (!$duration) {

            session()->forget(
                'subscription'
            );


            return redirect()
                ->route(
                    'public.subscription.show',
                    $package->slug
                )
                ->with(
                    'error',
                    'Durasi yang dipilih sudah tidak tersedia. Silakan pilih durasi kembali.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Harga
        |--------------------------------------------------------------------------
        */

        $price =
            $duration->discount_price
            ?? $duration->price;


        /*
        |--------------------------------------------------------------------------
        | Cek Pending Subscription
        |--------------------------------------------------------------------------
        |
        | Kalau user sebelumnya sudah klik lanjut tetapi belum membayar,
        | jangan membuat subscription pending baru.
        |
        | Kita gunakan pending terakhir dengan paket/durasi yang sama.
        |
        */

        $existingSubscription = Subscription::where(
            'merchant_id',
            $user->merchant_id
        )
            ->where(
                'package_duration_id',
                $duration->id
            )
            ->where(
                'status',
                'pending'
            )
            ->latest('id')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Kalau Pending Sudah Ada
        |--------------------------------------------------------------------------
        */

        if ($existingSubscription) {

            /*
            |--------------------------------------------------------------------------
            | Bersihkan Session
            |--------------------------------------------------------------------------
            */

            session()->forget([
                'subscription.package_id',
                'subscription.duration_id',
                'subscription.from_public',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Lanjut Payment
            |--------------------------------------------------------------------------
            */

            return redirect()->route(
                'public.subscription.payment',
                encryptId(
                    $existingSubscription->id
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Buat Subscription Baru
        |--------------------------------------------------------------------------
        */

        $subscription = Subscription::create([

            'merchant_id' =>
                $user->merchant_id,

            'package_duration_id' =>
                $duration->id,

            'invoice_number' =>
                null,

            'start_date' =>
                null,

            'end_date' =>
                null,

            'price' =>
                $price,

            'paid_at' =>
                null,

            'status' =>
                'pending',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Pastikan Subscription Berhasil Dibuat
        |--------------------------------------------------------------------------
        */

        if (!$subscription) {

            return redirect()
                ->route(
                    'public.subscription.index'
                )
                ->with(
                    'error',
                    'Subscription gagal dibuat. Silakan coba kembali.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Hapus Session Setelah Berhasil
        |--------------------------------------------------------------------------
        |
        | Jangan hapus sebelum subscription berhasil dibuat.
        |
        */

        session()->forget([
            'subscription.package_id',
            'subscription.duration_id',
            'subscription.from_public',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Langsung Ke Payment
        |--------------------------------------------------------------------------
        */

        return redirect()->route(
            'public.subscription.payment',
            encryptId(
                $subscription->id
            )
        );
    }
}
