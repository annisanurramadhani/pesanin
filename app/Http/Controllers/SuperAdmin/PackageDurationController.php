<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDuration;
use App\Rules\SecureText;
use Illuminate\Http\Request;

class PackageDurationController extends Controller
{
    /**
     * Display durations for a package.
     */
    public function index($package)
    {
        $packageId = decryptId($package);

        abort_unless($packageId, 404);

        $package = Package::findOrFail($packageId);

        $durations = $package->durations()
            ->orderBy('sort_order')
            ->paginate(10);

        return view(
            'super_admin.packages.durations.index',
            compact('package', 'durations')
        );
    }


    /**
     * Show the form for creating a duration.
     */
    public function create($package)
    {
        $packageId = decryptId($package);

        abort_unless($packageId, 404);

        $package = Package::findOrFail($packageId);

        return view(
            'super_admin.packages.durations.create',
            compact('package')
        );
    }


    /**
     * Store a new duration.
     */
    public function store(Request $request, $package)
    {
        $packageId = decryptId($package);

        abort_unless($packageId, 404);

        $package = Package::findOrFail($packageId);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                new SecureText,
            ],

            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:price',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $package->durations()->create($validated);

        return redirect()
            ->route(
                'super_admin.packages.durations.index',
                encryptId($package->id)
            )
            ->with(
                'success',
                'Durasi Paket Berhasil ditambahkan.'
            );
    }


    /**
     * Show the form for editing a duration.
     */
    public function edit($package, $duration)
    {
        $packageId = decryptId($package);
        $durationId = decryptId($duration);

        abort_unless(
            $packageId && $durationId,
            404
        );

        $package = Package::findOrFail($packageId);
        $duration = PackageDuration::findOrFail($durationId);

        abort_unless(
            $duration->package_id === $package->id,
            404
        );

        return view(
            'super_admin.packages.durations.edit',
            compact('package', 'duration')
        );
    }


    /**
     * Update a duration.
     */
    public function update(
        Request $request,
        $package,
        $duration
    ) {
        $packageId = decryptId($package);
        $durationId = decryptId($duration);

        abort_unless(
            $packageId && $durationId,
            404
        );

        $package = Package::findOrFail($packageId);
        $duration = PackageDuration::findOrFail($durationId);

        abort_unless(
            $duration->package_id === $package->id,
            404
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                new SecureText,
            ],

            'duration_days' => [
                'required',
                'integer',
                'min:1',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:price',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $duration->update($validated);

        return redirect()
            ->route(
                'super_admin.packages.durations.index',
                encryptId($package->id)
            )
            ->with(
                'success',
                'Durasi Paket Berhasil diperbarui.'
            );
    }


    /**
     * Delete a duration.
     */
    public function destroy($package, $duration)
    {
        $packageId = decryptId($package);
        $durationId = decryptId($duration);

        abort_unless(
            $packageId && $durationId,
            404
        );

        $package = Package::findOrFail($packageId);
        $duration = PackageDuration::findOrFail($durationId);

        abort_unless(
            $duration->package_id === $package->id,
            404
        );

        $duration->delete();

        return redirect()
            ->route(
                'super_admin.packages.durations.index',
                encryptId($package->id)
            )
            ->with(
                'success',
                'Durasi Paket Berhasil dihapus.'
            );
    }
}
