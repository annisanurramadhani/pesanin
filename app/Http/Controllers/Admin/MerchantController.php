<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Rules\SecureText;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::with([
            'users',
            'activeSubscription.packageDuration.package',
        ])
            ->latest()
            ->paginate(10);

        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('admin.merchants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:merchants,name',
                new SecureText,
            ],
            'phone' => [
                'required',
                'digits_between:10,20',
                'regex:/^[0-9]+$/',
            ],
            'address' => [
                'required',
                'string',
                'max:1000',
                new SecureText,
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ], [
            'name.required' => 'Nama merchant wajib diisi.',
            'name.string' => 'Nama merchant harus berupa teks.',
            'name.max' => 'Nama merchant maksimal 255 karakter.',
            'name.unique' => 'Nama merchant sudah terdaftar.',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.digits_between' => 'Nomor telepon harus memiliki 10 sampai 20 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',

            'address.required' => 'Alamat merchant wajib diisi.',
            'address.string' => 'Alamat merchant harus berupa teks.',
            'address.max' => 'Alamat merchant maksimal 1000 karakter.',

            'status.required' => 'Status merchant wajib dipilih.',
            'status.in' => 'Status merchant tidak valid.',
        ]);

        Merchant::create([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['name']),
            'phone' => $validated['phone'],
            'address' => trim($validated['address']),
            'status' => $validated['status'],
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
                new SecureText,
            ],
            'phone' => [
                'required',
                'digits_between:10,20',
                'regex:/^[0-9]+$/',
            ],
            'address' => [
                'required',
                'string',
                'max:1000',
                new SecureText,
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ], [
            'name.required' => 'Nama merchant wajib diisi.',
            'name.string' => 'Nama merchant harus berupa teks.',
            'name.max' => 'Nama merchant maksimal 255 karakter.',
            'name.unique' => 'Nama merchant sudah digunakan merchant lain.',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.digits_between' => 'Nomor telepon harus memiliki 10 sampai 20 digit.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',

            'address.required' => 'Alamat merchant wajib diisi.',
            'address.string' => 'Alamat merchant harus berupa teks.',
            'address.max' => 'Alamat merchant maksimal 1000 karakter.',

            'status.required' => 'Status merchant wajib dipilih.',
            'status.in' => 'Status merchant tidak valid.',
        ]);

        $merchant->update([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['name']),
            'phone' => $validated['phone'],
            'address' => trim($validated['address']),
            'status' => $validated['status'],
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
