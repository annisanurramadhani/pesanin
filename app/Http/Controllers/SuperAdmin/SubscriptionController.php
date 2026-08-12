<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\PackageDuration;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with([
            'merchant',
            'packageDuration.package',
        ])
            ->latest()
            ->paginate(10);

        return view('super_admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $merchants = Merchant::where('status', 'active')
            ->orderBy('name')
            ->get();

        $packageDurations = PackageDuration::with('package')
            ->where('status', 'active')
            ->orderBy('package_id')
            ->orderBy('sort_order')
            ->get();

        return view('super_admin.subscriptions.create', compact(
            'merchants',
            'packageDurations'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'package_duration_id' => [
                'required',
                'exists:package_durations,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:active,expired,cancelled',
            ],
        ], [
            'merchant_id.required' => 'Merchant wajib dipilih.',
            'merchant_id.exists' => 'Merchant yang dipilih tidak ditemukan.',

            'package_duration_id.required' => 'Paket dan durasi wajib dipilih.',
            'package_duration_id.exists' => 'Paket dan durasi yang dipilih tidak ditemukan.',

            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',

            'status.required' => 'Status langganan wajib dipilih.',
            'status.in' => 'Status langganan tidak valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil data durasi paket dari database
        |--------------------------------------------------------------------------
        */

        $packageDuration = PackageDuration::findOrFail(
            $validated['package_duration_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Hitung tanggal berakhir otomatis
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::parse($validated['start_date']);

        $endDate = $startDate
            ->copy()
            ->addDays($packageDuration->duration_days - 1);

        /*
        |--------------------------------------------------------------------------
        | Tentukan harga dari database
        |--------------------------------------------------------------------------
        */

        $price = $packageDuration->discount_price
            ?? $packageDuration->price;

        /*
        |--------------------------------------------------------------------------
        | Buat subscription
        |--------------------------------------------------------------------------
        */

        Subscription::create([
            'merchant_id' => $validated['merchant_id'],
            'package_duration_id' => $validated['package_duration_id'],
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'price' => $price,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('super_admin.subscriptions.index')
            ->with('success', 'Langganan berhasil ditambahkan.');
    }

    public function show(string $encryptedId)
    {
        try {
            $subscriptionId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $subscription = Subscription::with([
            'merchant',
            'packageDuration.package',
        ])->findOrFail($subscriptionId);

        return view('super_admin.subscriptions.show', compact('subscription'));
    }

    public function edit(string $encryptedId)
    {
        try {
            $subscriptionId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $subscription = Subscription::with([
            'merchant',
            'packageDuration.package',
        ])->findOrFail($subscriptionId);

        $merchants = Merchant::orderBy('name')->get();

        $packageDurations = PackageDuration::with('package')
            ->where('status', 'active')
            ->orderBy('package_id')
            ->orderBy('sort_order')
            ->get();

        return view('super_admin.subscriptions.edit', compact(
            'subscription',
            'merchants',
            'packageDurations'
        ));
    }

    public function update(Request $request, string $encryptedId)
    {
        try {
            $subscriptionId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $subscription = Subscription::findOrFail($subscriptionId);

        $validated = $request->validate([
            'merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'package_duration_id' => [
                'required',
                'exists:package_durations,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:active,expired,cancelled',
            ],
        ], [
            'merchant_id.required' => 'Merchant wajib dipilih.',
            'merchant_id.exists' => 'Merchant yang dipilih tidak ditemukan.',

            'package_duration_id.required' => 'Paket dan durasi wajib dipilih.',
            'package_duration_id.exists' => 'Paket dan durasi yang dipilih tidak ditemukan.',

            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai tidak valid.',

            'status.required' => 'Status langganan wajib dipilih.',
            'status.in' => 'Status langganan tidak valid.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil durasi paket terbaru dari database
        |--------------------------------------------------------------------------
        */

        $packageDuration = PackageDuration::findOrFail(
            $validated['package_duration_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Hitung tanggal berakhir otomatis
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::parse($validated['start_date']);

        $endDate = $startDate
            ->copy()
            ->addDays($packageDuration->duration_days - 1);

        /*
        |--------------------------------------------------------------------------
        | Ambil harga dari database
        |--------------------------------------------------------------------------
        */

        $price = $packageDuration->discount_price
            ?? $packageDuration->price;

        /*
        |--------------------------------------------------------------------------
        | Update subscription
        |--------------------------------------------------------------------------
        */

        $subscription->update([
            'merchant_id' => $validated['merchant_id'],
            'package_duration_id' => $validated['package_duration_id'],
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'price' => $price,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('super_admin.subscriptions.index')
            ->with('success', 'Langganan berhasil diperbarui.');
    }

    public function destroy(string $encryptedId)
    {
        try {
            $subscriptionId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $subscription = Subscription::findOrFail($subscriptionId);

        $subscription->delete();

        return redirect()
            ->route('super_admin.subscriptions.index')
            ->with('success', 'Langganan berhasil dihapus.');
    }
}
