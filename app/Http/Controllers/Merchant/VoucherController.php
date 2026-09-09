<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Rules\SecureText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar voucher merchant.
     */
    public function index()
    {
        $merchant = Auth::user()->merchant;

        $vouchers = Voucher::where(
            'merchant_id',
            $merchant->id
        )
            ->latest()
            ->get();

        return view(
            'merchant.voucher.index',
            compact(
                'merchant',
                'vouchers'
            )
        );
    }

    /**
     * Menampilkan form tambah voucher.
     */
    public function create()
    {
        return view(
            'merchant.voucher.create'
        );
    }

    /**
     * Menampilkan form edit voucher.
     */
    public function edit($id)
    {
        $merchant = Auth::user()->merchant;

        $id = Crypt::decryptString($id);

        $voucher = Voucher::where(
            'merchant_id',
            $merchant->id
        )->findOrFail($id);

        $encryptedId = Crypt::encryptString(
            (string) $voucher->id
        );

        return view(
            'merchant.voucher.edit',
            compact(
                'merchant',
                'voucher',
                'encryptedId'
            )
        );
    }

    /**
     * Menyimpan voucher baru.
     */
    public function store(Request $request)
    {
        $merchant = Auth::user()->merchant;

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                new SecureText,
            ],

            'type' => [
                'required',
                'in:percentage,fixed',
            ],

            'value' => [
                'required',
                'numeric',
                'min:1',
            ],

            'min_order_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'max_discount_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'usage_limit' => [
                'required',
                'integer',
                'min:1',
            ],

            'starts_at' => [
                'required',
                'date',
            ],

            'expires_at' => [
                'required',
                'date',
                'after_or_equal:starts_at',
            ],
        ], [
            'code.required' =>
                'Kode voucher wajib diisi.',

            'code.max' =>
                'Kode voucher maksimal 50 karakter.',
            
            'code.regex' =>
                'Kode voucher hanya boleh berisi huruf, angka, tanda strip (-), dan underscore (_).',

            'type.required' =>
                'Tipe diskon wajib dipilih.',

            'type.in' =>
                'Tipe diskon tidak valid.',

            'value.required' =>
                'Nilai diskon wajib diisi.',

            'value.numeric' =>
                'Nilai diskon harus berupa angka.',

            'value.min' =>
                'Nilai diskon minimal 1.',

            'min_order_amount.required' =>
                'Minimal pembelian wajib diisi.',

            'min_order_amount.numeric' =>
                'Minimal pembelian harus berupa angka.',

            'min_order_amount.min' =>
                'Minimal pembelian tidak boleh kurang dari 0.',

            'max_discount_amount.numeric' =>
                'Maksimal diskon harus berupa angka.',

            'max_discount_amount.min' =>
                'Maksimal diskon tidak boleh kurang dari 0.',

            'usage_limit.integer' =>
                'Batas penggunaan harus berupa angka bulat.',

            'max_discount_amount.required' =>
                'Maksimal diskon wajib diisi.',

            'usage_limit.required' =>
                'Batas penggunaan wajib diisi.',

            'usage_limit.min' =>
                'Batas penggunaan minimal 1.',

            'starts_at.required' =>
                'Tanggal mulai wajib diisi.',

            'starts_at.date' =>
                'Tanggal mulai tidak valid.',

            'expires_at.required' =>
                'Tanggal berakhir wajib diisi.',

            'expires_at.date' =>
                'Tanggal berakhir tidak valid.',

            'expires_at.after_or_equal' =>
                'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | NORMALISASI KODE
        |--------------------------------------------------------------------------
        */

        $validated['code'] = strtoupper(
            trim($validated['code'])
        );

        /*
        |--------------------------------------------------------------------------
        | CEK KODE DUPLIKAT DALAM MERCHANT
        |--------------------------------------------------------------------------
        */

        $exists = Voucher::where(
            'merchant_id',
            $merchant->id
        )
            ->where(
                'code',
                $validated['code']
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'code' =>
                        'Kode voucher tersebut sudah digunakan.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI NILAI DISKON
        |--------------------------------------------------------------------------
        */

        if (
            $validated['type'] === 'percentage'
            &&
            $validated['value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'value' =>
                        'Diskon persentase tidak boleh lebih dari 100%.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN VOUCHER
        |--------------------------------------------------------------------------
        */

        Voucher::create([
            'merchant_id' =>
                $merchant->id,

            'code' =>
                $validated['code'],

            'type' =>
                $validated['type'],

            'value' =>
                $validated['value'],

            'min_order_amount' =>
                $validated['min_order_amount'],

            'max_discount_amount' =>
                $validated['max_discount_amount']
                    ?? null,

            'usage_limit' =>
                $validated['usage_limit']
                    ?? null,

            'used_count' =>
                0,

            'starts_at' =>
                $validated['starts_at']
                    ?? null,

            'expires_at' =>
                $validated['expires_at']
                    ?? null,

            'status' =>
                $request->has('status')
                    ? 'active'
                    : 'inactive',
        ]);

        return redirect()
            ->route('merchant.voucher.index')
            ->with(
                'success',
                'Voucher berhasil ditambahkan.'
            );
    }

    /**
     * Memperbarui voucher.
     */
    public function update(Request $request, $id)
    {
        $merchant = Auth::user()->merchant;

        $id = Crypt::decryptString($id);

        $voucher = Voucher::where(
            'merchant_id',
            $merchant->id
        )->findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                new SecureText,
            ],
            'type' => [
                'required',
                'in:percentage,fixed',
            ],
            'value' => [
                'required',
                'numeric',
                'min:1',
            ],
            'min_order_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'max_discount_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'usage_limit' => [
                'required',
                'integer',
                'min:1',
            ],
            'starts_at' => [
                'required',
                'date',
            ],
            'expires_at' => [
                'required',
                'date',
                'after_or_equal:starts_at',
            ],
        ], [
            'code.required' =>
                'Kode voucher wajib diisi.',

            'code.max' =>
                'Kode voucher maksimal 50 karakter.',

            'code.regex' =>
                'Kode voucher hanya boleh berisi huruf, angka, tanda strip (-), dan underscore (_).',

            'type.required' =>
                'Tipe diskon wajib dipilih.',
   
            'type.in' =>
                'Tipe diskon tidak valid.',

            'value.required' =>
                'Nilai diskon wajib diisi.',

            'value.numeric' =>
                'Nilai diskon harus berupa angka.',

            'value.min' =>
                'Nilai diskon minimal 1.',

            'min_order_amount.required' =>
                'Minimal pembelian wajib diisi.',

            'min_order_amount.numeric' =>
                'Minimal pembelian harus berupa angka.',

            'min_order_amount.min' =>
                'Minimal pembelian tidak boleh kurang dari 0.',

            'max_discount_amount.numeric' =>
                'Maksimal diskon harus berupa angka.',

            'max_discount_amount.min' =>
                'Maksimal diskon tidak boleh kurang dari 0.',

            'usage_limit.integer' =>
                'Batas penggunaan harus berupa angka bulat.',

            'max_discount_amount.required' =>
                'Maksimal diskon wajib diisi.',

            'usage_limit.required' =>
                'Batas penggunaan wajib diisi.',

            'starts_at.required' =>
                'Tanggal mulai wajib diisi.',

            'expires_at.required' =>
                'Tanggal berakhir wajib diisi.',

            'usage_limit.min' =>
                'Batas penggunaan minimal 1.',

            'starts_at.date' =>
                'Tanggal mulai tidak valid.',

            'expires_at.date' =>
                'Tanggal berakhir tidak valid.',

            'expires_at.after_or_equal' =>
                'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
        ]);

        $validated['code'] = strtoupper(
            trim($validated['code'])
        );

        // Cek apakah kode sudah digunakan voucher lain
        $exists = Voucher::where(
            'merchant_id',
            $merchant->id
        )
            ->where(
                'code',
                $validated['code']
            )
            ->where(
                'id',
                '!=',
                $voucher->id
            )
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'code' =>
                        'Kode voucher tersebut sudah digunakan.',
                ]);
        }

        // Validasi maksimal persentase
        if (
            $validated['type'] === 'percentage'
            &&
            $validated['value'] > 100
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'value' =>
                        'Diskon persentase tidak boleh lebih dari 100%.',
                ]);
        }
        
            /*
            |--------------------------------------------------------------------------
            | VALIDASI AKTIVASI VOUCHER
            |--------------------------------------------------------------------------
            */

            if (
                $request->has('status')
                &&
                !is_null($validated['usage_limit'])
                &&
                $voucher->used_count >=
                $validated['usage_limit']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'status' =>
                            'Voucher tidak dapat diaktifkan karena batas penggunaan sudah habis. Tingkatkan batas penggunaan terlebih dahulu.',
                    ]);
            }

        //update voucher
        $voucher->update([
            'code' =>
                $validated['code'],

            'type' =>
                $validated['type'],

            'value' =>
                $validated['value'],

            'min_order_amount' =>
                $validated['min_order_amount'],

            'max_discount_amount' =>
                $validated['max_discount_amount']
                    ?? null,

            'usage_limit' =>
                $validated['usage_limit']
                    ?? null,

            'starts_at' =>
                $validated['starts_at']
                    ?? null,

            'expires_at' =>
                $validated['expires_at']
                    ?? null,

            'status' =>
                $request->has('status')
                    ? 'active'
                    : 'inactive',
        ]);

        return redirect()
            ->route('merchant.voucher.index')
            ->with(
                'success',
                'Voucher berhasil diperbarui.'
            );
    }

    /**
     * Menghapus voucher.
     */
    public function destroy($id)
    {
        $merchant = Auth::user()->merchant;

        $voucher = Voucher::where(
            'merchant_id',
            $merchant->id
        )->findOrFail($id);

        $voucher->delete();

        return redirect()
            ->route('merchant.voucher.index')
            ->with(
                'success',
                'Voucher berhasil dihapus.'
            );
    }
}