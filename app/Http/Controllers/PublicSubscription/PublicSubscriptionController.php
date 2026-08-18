<?php

namespace App\Http\Controllers\PublicSubscription;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDuration;

class PublicSubscriptionController extends Controller
{
    /**
     * Halaman utama public subscription.
     */
    public function index()
    {
        $packages = Package::where('status', 'active')
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
        $package = Package::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $durations = PackageDuration::where('package_id', $package->id)
            ->where('status', 'active')
            ->orderBy('duration_days')
            ->get();

        return view(
            'public_subscription.duration',
            compact('package', 'durations')
        );
    }

    /**
     * Menampilkan ringkasan pesanan.
     */
    public function summary(string $slug, string $encryptedDuration)
    {
        $package = Package::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $durationId = decryptId($encryptedDuration);

        if (!$durationId) {
            abort(404);
        }

        $duration = PackageDuration::where('id', $durationId)
            ->where('package_id', $package->id)
            ->where('status', 'active')
            ->firstOrFail();

        $price = $duration->discount_price ?? $duration->price;

        $hasDiscount = !is_null($duration->discount_price)
            && $duration->discount_price < $duration->price;

        /*
         * Simpan pilihan subscription sementara.
         * Data ini akan digunakan setelah user login/register.
         */
        session([
            'subscription.package_id' => $package->id,
            'subscription.duration_id' => $duration->id,
        ]);

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
     * Memilih apakah user sudah memiliki akun atau belum.
     */
    public function account(string $encryptedDuration)
    {
        $durationId = decryptId($encryptedDuration);

        if (!$durationId) {
            abort(404);
        }

        $duration = PackageDuration::with('package')
            ->where('id', $durationId)
            ->where('status', 'active')
            ->firstOrFail();

        /*
         * Pastikan pilihan durasi tetap tersimpan
         * ketika user masuk ke halaman account.
         */
        session([
            'subscription.package_id' => $duration->package_id,
            'subscription.duration_id' => $duration->id,
        ]);

        return view(
            'public_subscription.account',
            compact('duration')
        );
    }
}
