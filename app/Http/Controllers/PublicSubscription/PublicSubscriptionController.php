<?php

namespace App\Http\Controllers\PublicSubscription;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDuration;

class PublicSubscriptionController extends Controller
{
    /**
     * Halaman utama public subscription.
     * Menampilkan jenis paket saja.
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

}
