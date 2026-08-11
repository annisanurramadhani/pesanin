<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::latest()->paginate(10);

        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('admin.merchants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:merchants,name'],
            'phone' => ['required', 'digits_between:10,20'],
            'address' => ['required', 'string', 'max:1000'],
            'subscription_expires_at' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'name.required' => 'Nama merchant wajib diisi.',
            'name.string' => 'Nama merchant harus berupa teks.',
            'name.max' => 'Nama merchant maksimal 255 karakter.',
            'name.unique' => 'Nama merchant sudah terdaftar.',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.digits_between' => 'Nomor telepon harus memiliki 10 sampai 20 digit.',

            'address.required' => 'Alamat merchant wajib diisi.',
            'address.string' => 'Alamat merchant harus berupa teks.',
            'address.max' => 'Alamat merchant maksimal 1000 karakter.',

            'subscription_expires_at.required' => 'Masa langganan wajib diisi.',
            'subscription_expires_at.date' => 'Masa langganan harus berupa tanggal yang valid.',
            'subscription_expires_at.after_or_equal' => 'Masa langganan tidak boleh sebelum hari ini.',
        ]);

        Merchant::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'is_active' => $request->boolean('is_active'),
            'subscription_expires_at' => $validated['subscription_expires_at'],
        ]);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant berhasil ditambahkan.');
    }

    public function edit(string $encryptedId)
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);

        try {
            $merchantId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $merchant = Merchant::findOrFail($merchantId);

        return view('admin.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, string $encryptedId)
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);

        try {
            $merchantId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $merchant = Merchant::findOrFail($merchantId);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:merchants,name,' . $merchant->id,
            ],
            'phone' => ['required', 'digits_between:10,20'],
            'address' => ['required', 'string', 'max:1000'],
            'subscription_expires_at' => ['required', 'date', 'after_or_equal:today'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Nama merchant wajib diisi.',
            'name.string' => 'Nama merchant harus berupa teks.',
            'name.max' => 'Nama merchant maksimal 255 karakter.',
            'name.unique' => 'Nama merchant sudah digunakan merchant lain.',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.digits_between' => 'Nomor telepon harus memiliki 10 sampai 20 digit.',

            'address.required' => 'Alamat merchant wajib diisi.',
            'address.string' => 'Alamat merchant harus berupa teks.',
            'address.max' => 'Alamat merchant maksimal 1000 karakter.',

            'subscription_expires_at.required' => 'Masa langganan wajib diisi.',
            'subscription_expires_at.date' => 'Masa langganan harus berupa tanggal yang valid.',
            'subscription_expires_at.after_or_equal' => 'Masa langganan tidak boleh sebelum hari ini.',

            'is_active.required' => 'Status merchant wajib dipilih.',
            'is_active.boolean' => 'Status merchant tidak valid.',
        ]);

        $merchant->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'subscription_expires_at' => $validated['subscription_expires_at'],
            'is_active' => (bool) $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant berhasil diperbarui.');
    }

    public function destroy(string $encryptedId)
    {
        abort_unless(auth()->user()->role === 'super_admin', 403);

        try {
            $merchantId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $merchant = Merchant::findOrFail($merchantId);

        $merchant->delete();

        return redirect()
            ->route('admin.merchants.index')
            ->with('success', 'Merchant berhasil dihapus.');
    }
}
