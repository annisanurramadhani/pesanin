<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDuration;
use Illuminate\Http\Request;

class PackageDurationController extends Controller
{
    /**
     * Display durations for a package.
     */
    public function index(Package $package)
    {
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
    public function create(Package $package)
    {
        return view(
            'super_admin.packages.durations.create',
            compact('package')
        );
    }

    /**
     * Store a new duration.
     */
    public function store(
        Request $request,
        Package $package
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
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
                $package
            )
            ->with(
                'success',
                'Package duration created successfully.'
            );
    }

    /**
     * Show the form for editing a duration.
     */
    public function edit(
        Package $package,
        PackageDuration $duration
    ) {
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
        Package $package,
        PackageDuration $duration
    ) {
        abort_unless(
            $duration->package_id === $package->id,
            404
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
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
                $package
            )
            ->with(
                'success',
                'Package duration updated successfully.'
            );
    }

    /**
     * Delete a duration.
     */
    public function destroy(
        Package $package,
        PackageDuration $duration
    ) {
        abort_unless(
            $duration->package_id === $package->id,
            404
        );

        $duration->delete();

        return redirect()
            ->route(
                'super_admin.packages.durations.index',
                $package
            )
            ->with(
                'success',
                'Package duration deleted successfully.'
            );
    }
}
