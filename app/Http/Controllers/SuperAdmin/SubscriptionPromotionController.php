<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PackageDuration;
use App\Models\SubscriptionPromotion;
use App\Rules\SecureText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class SubscriptionPromotionController extends Controller
{
    /**
     * Display all subscription promotions.
     */
    public function index()
    {
        $promotions = SubscriptionPromotion::query()
            ->withCount('durations')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view(
            'super_admin.subscription_promotions.index',
            compact('promotions')
        );
    }


    /**
     * Show the form for creating a promotion.
     */
    public function create()
    {
        $durations = PackageDuration::query()
            ->with('package')
            ->where('status', 'active')
            ->orderBy('package_id')
            ->orderBy('duration_days')
            ->get();

        return view(
            'super_admin.subscription_promotions.create',
            compact('durations')
        );
    }


    /**
     * Store a new promotion.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                new SecureText(),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
                new SecureText(),
            ],

            'discount_type' => [
                'required',
                Rule::in([
                    'percentage',
                    'fixed',
                ]),
            ],

            'discount_value' => [
                'required',
                'numeric',
                'min:0',
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'ends_at' => [
                'required',
                'date',
                'after:starts_at',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'duration_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'duration_ids.*' => [
                'integer',
                'exists:package_durations,id',
            ],
        ], [
            'name.required' =>
                'Nama promo wajib diisi.',

            'name.min' =>
                'Nama promo minimal 2 karakter.',

            'name.max' =>
                'Nama promo maksimal 100 karakter.',

            'description.max' =>
                'Deskripsi promo maksimal 1000 karakter.',

            'discount_type.required' =>
                'Tipe diskon wajib dipilih.',

            'discount_type.in' =>
                'Tipe diskon tidak valid.',

            'discount_value.required' =>
                'Nilai diskon wajib diisi.',

            'discount_value.numeric' =>
                'Nilai diskon harus berupa angka.',

            'discount_value.min' =>
                'Nilai diskon tidak boleh kurang dari 0.',

            'starts_at.required' =>
                'Tanggal mulai promo wajib diisi.',

            'starts_at.date' =>
                'Tanggal mulai promo tidak valid.',

            'ends_at.required' =>
                'Tanggal berakhir promo wajib diisi.',

            'ends_at.date' =>
                'Tanggal berakhir promo tidak valid.',

            'ends_at.after' =>
                'Tanggal berakhir harus setelah tanggal mulai.',

            'status.required' =>
                'Status promo wajib dipilih.',

            'status.in' =>
                'Status promo tidak valid.',

            'duration_ids.required' =>
                'Minimal pilih satu durasi paket.',

            'duration_ids.array' =>
                'Data durasi paket tidak valid.',

            'duration_ids.min' =>
                'Minimal pilih satu durasi paket.',

            'duration_ids.*.exists' =>
                'Durasi paket yang dipilih tidak tersedia.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Validasi khusus percentage
        |--------------------------------------------------------------------------
        |
        | Persentase tidak boleh lebih dari 100%.
        |
        */

        if (
            $validated['discount_type'] === 'percentage'
            &&
            (float) $validated['discount_value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'discount_value' =>
                        'Diskon persentase tidak boleh lebih dari 100%.',
                ]);
        }


        try {

            DB::transaction(function () use ($validated) {

                /*
                |--------------------------------------------------------------------------
                | Buat Promotion
                |--------------------------------------------------------------------------
                */

                $promotion = SubscriptionPromotion::create([
                    'name' =>
                        trim($validated['name']),

                    'description' =>
                        isset($validated['description'])
                            ? trim($validated['description'])
                            : null,

                    'discount_type' =>
                        $validated['discount_type'],

                    'discount_value' =>
                        $validated['discount_value'],

                    'starts_at' =>
                        $validated['starts_at'],

                    'ends_at' =>
                        $validated['ends_at'],

                    'status' =>
                        $validated['status'],
                ]);


                /*
                |--------------------------------------------------------------------------
                | Hubungkan promotion dengan durations
                |--------------------------------------------------------------------------
                */

                $promotion->durations()->sync(
                    $validated['duration_ids']
                );
            });


            return redirect()
                ->route(
                    'super_admin.subscription_promotions.index'
                )
                ->with(
                    'success',
                    'Promo subscription berhasil dibuat.'
                );

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Promo subscription gagal dibuat. Silakan coba lagi.'
                );
        }
    }


    /**
     * Show the form for editing a promotion.
     */
    public function edit(string $encryptedId)
    {
        $id = decryptId($encryptedId);

        abort_if(!$id, 404);

        $promotion = SubscriptionPromotion::with(
            'durations'
        )->findOrFail($id);

        $durations = PackageDuration::query()
            ->with('package')
            ->where('status', 'active')
            ->orderBy('package_id')
            ->orderBy('duration_days')
            ->get();

        $selectedDurationIds = $promotion
            ->durations
            ->pluck('id')
            ->toArray();

        return view(
            'super_admin.subscription_promotions.edit',
            compact(
                'promotion',
                'durations',
                'selectedDurationIds',
                'encryptedId'
            )
        );
    }


    /**
     * Update a promotion.
     */
    public function update(
        Request $request,
        string $encryptedId
    ): RedirectResponse {

        $id = decryptId($encryptedId);

        abort_if(!$id, 404);

        $promotion = SubscriptionPromotion::findOrFail($id);


        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                new SecureText(),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
                new SecureText(),
            ],

            'discount_type' => [
                'required',
                Rule::in([
                    'percentage',
                    'fixed',
                ]),
            ],

            'discount_value' => [
                'required',
                'numeric',
                'min:0',
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'ends_at' => [
                'required',
                'date',
                'after:starts_at',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'duration_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'duration_ids.*' => [
                'integer',
                'exists:package_durations,id',
            ],
        ], [
            'name.required' =>
                'Nama promo wajib diisi.',

            'name.min' =>
                'Nama promo minimal 2 karakter.',

            'name.max' =>
                'Nama promo maksimal 100 karakter.',

            'description.max' =>
                'Deskripsi promo maksimal 1000 karakter.',

            'discount_type.required' =>
                'Tipe diskon wajib dipilih.',

            'discount_type.in' =>
                'Tipe diskon tidak valid.',

            'discount_value.required' =>
                'Nilai diskon wajib diisi.',

            'discount_value.numeric' =>
                'Nilai diskon harus berupa angka.',

            'discount_value.min' =>
                'Nilai diskon tidak boleh kurang dari 0.',

            'starts_at.required' =>
                'Tanggal mulai promo wajib diisi.',

            'starts_at.date' =>
                'Tanggal mulai promo tidak valid.',

            'ends_at.required' =>
                'Tanggal berakhir promo wajib diisi.',

            'ends_at.date' =>
                'Tanggal berakhir promo tidak valid.',

            'ends_at.after' =>
                'Tanggal berakhir harus setelah tanggal mulai.',

            'status.required' =>
                'Status promo wajib dipilih.',

            'status.in' =>
                'Status promo tidak valid.',

            'duration_ids.required' =>
                'Minimal pilih satu durasi paket.',

            'duration_ids.array' =>
                'Data durasi paket tidak valid.',

            'duration_ids.min' =>
                'Minimal pilih satu durasi paket.',

            'duration_ids.*.exists' =>
                'Durasi paket yang dipilih tidak tersedia.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Validasi khusus percentage
        |--------------------------------------------------------------------------
        */

        if (
            $validated['discount_type'] === 'percentage'
            &&
            (float) $validated['discount_value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'discount_value' =>
                        'Diskon persentase tidak boleh lebih dari 100%.',
                ]);
        }


        try {

            DB::transaction(function () use (
                $promotion,
                $validated
            ) {

                /*
                |--------------------------------------------------------------------------
                | Update promotion
                |--------------------------------------------------------------------------
                */

                $promotion->update([
                    'name' =>
                        trim($validated['name']),

                    'description' =>
                        isset($validated['description'])
                            ? trim($validated['description'])
                            : null,

                    'discount_type' =>
                        $validated['discount_type'],

                    'discount_value' =>
                        $validated['discount_value'],

                    'starts_at' =>
                        $validated['starts_at'],

                    'ends_at' =>
                        $validated['ends_at'],

                    'status' =>
                        $validated['status'],
                ]);


                /*
                |--------------------------------------------------------------------------
                | Sinkronisasi durations
                |--------------------------------------------------------------------------
                */

                $promotion->durations()->sync(
                    $validated['duration_ids']
                );
            });


            return redirect()
                ->route(
                    'super_admin.subscription_promotions.index'
                )
                ->with(
                    'success',
                    'Promo subscription berhasil diperbarui.'
                );

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Promo subscription gagal diperbarui.'
                );
        }
    }


    /**
     * Delete a promotion.
     */
    public function destroy(
        string $encryptedId
    ): RedirectResponse {

        $id = decryptId($encryptedId);

        abort_if(!$id, 404);

        $promotion = SubscriptionPromotion::findOrFail($id);


        try {

            DB::transaction(function () use ($promotion) {

                /*
                |--------------------------------------------------------------------------
                | Hapus relationship pivot
                |--------------------------------------------------------------------------
                */

                $promotion->durations()->detach();

                /*
                |--------------------------------------------------------------------------
                | Soft delete promotion
                |--------------------------------------------------------------------------
                */

                $promotion->delete();
            });


            return redirect()
                ->route(
                    'super_admin.subscription_promotions.index'
                )
                ->with(
                    'success',
                    'Promo subscription berhasil dihapus.'
                );

        } catch (Throwable $e) {

            report($e);

            return back()
                ->with(
                    'error',
                    'Promo subscription tidak dapat dihapus.'
                );
        }
    }
}
