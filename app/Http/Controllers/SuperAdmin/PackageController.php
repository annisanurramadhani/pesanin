<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Rules\SecureText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::query()
            ->withCount('durations')
            ->orderBy('name')
            ->paginate(10);

        return view('super_admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('super_admin.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'unique:packages,name',
                new SecureText(),
            ],

            'badge' => [
                'nullable',
                'string',
                'max:50',
                new SecureText(),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
                new SecureText(),
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'max_qr_codes' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_menus' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_staff' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'name.min' => 'Nama paket minimal 2 karakter.',
            'name.max' => 'Nama paket maksimal 100 karakter.',
            'name.unique' => 'Nama paket sudah digunakan.',

            'badge.max' => 'Badge maksimal 50 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',

            'max_qr_codes.required' => 'Limit QR Code wajib diisi.',
            'max_qr_codes.integer' => 'Limit QR Code harus berupa angka.',
            'max_qr_codes.min' => 'Limit QR Code minimal 1.',

            'max_menus.required' => 'Limit Menu wajib diisi.',
            'max_menus.integer' => 'Limit Menu harus berupa angka.',
            'max_menus.min' => 'Limit Menu minimal 1.',

            'max_staff.required' => 'Limit Karyawan wajib diisi.',
            'max_staff.integer' => 'Limit Karyawan harus berupa angka.',
            'max_staff.min' => 'Limit Karyawan minimal 1.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                Package::create([
                    'name' => trim($validated['name']),
                    'slug' => Str::slug($validated['name']),
                    'badge' => isset($validated['badge'])
                        ? trim($validated['badge'])
                        : null,
                    'description' => isset($validated['description'])
                        ? trim($validated['description'])
                        : null,
                    'status' => $validated['status'],
                    'max_qr_codes' => $validated['max_qr_codes'],
                    'max_menus' => $validated['max_menus'],
                    'max_staff' => $validated['max_staff'],
                ]);
            });

            return redirect()
                ->route('super_admin.packages.index')
                ->with('success', 'Paket berhasil dibuat.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Paket gagal dibuat. Silakan coba lagi.');
        }
    }

    public function edit(string $encryptedId)
    {
        $id = decryptId($encryptedId);

        abort_if(!$id, 404);

        $package = Package::with('durations')->findOrFail($id);

        return view(
            'super_admin.packages.edit',
            compact('package', 'encryptedId')
        );
    }

    public function update(
        Request $request,
        string $encryptedId
    ): RedirectResponse {
        $id = decryptId($encryptedId);

        abort_if(!$id, 404);

        $package = Package::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('packages', 'name')->ignore($package->id),
                new SecureText(),
            ],

            'badge' => [
                'nullable',
                'string',
                'max:50',
                new SecureText(),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
                new SecureText(),
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'max_qr_codes' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_menus' => [
                'required',
                'integer',
                'min:1',
            ],

            'max_staff' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'name.required' => 'Nama paket wajib diisi.',
            'name.min' => 'Nama paket minimal 2 karakter.',
            'name.max' => 'Nama paket maksimal 100 karakter.',
            'name.unique' => 'Nama paket sudah digunakan.',

            'badge.max' => 'Badge maksimal 50 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',

            'max_qr_codes.required' => 'Limit QR Code wajib diisi.',
            'max_qr_codes.integer' => 'Limit QR Code harus berupa angka.',
            'max_qr_codes.min' => 'Limit QR Code minimal 1.',

            'max_menus.required' => 'Limit Menu wajib diisi.',
            'max_menus.integer' => 'Limit Menu harus berupa angka.',
            'max_menus.min' => 'Limit Menu minimal 1.',

            'max_staff.required' => 'Limit Karyawan wajib diisi.',
            'max_staff.integer' => 'Limit Karyawan harus berupa angka.',
            'max_staff.min' => 'Limit Karyawan minimal 1.',
        ]);

        try {
            DB::transaction(function () use ($package, $validated) {
                $nameChanged = $package->name !== trim($validated['name']);

                $package->update([
                    'name' => trim($validated['name']),
                    'slug' => $nameChanged
                        ? Str::slug($validated['name'])
                        : $package->slug,
                    'badge' => isset($validated['badge'])
                        ? trim($validated['badge'])
                        : null,
                    'description' => isset($validated['description'])
                        ? trim($validated['description'])
                        : null,
                    'status' => $validated['status'],
                    'max_qr_codes' => $validated['max_qr_codes'],
                    'max_menus' => $validated['max_menus'],
                    'max_staff' => $validated['max_staff'],
                ]);
            });

            return redirect()
                ->route('super_admin.packages.index')
                ->with('success', 'Paket berhasil diperbarui.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Paket gagal diperbarui.');
        }
    }

    public function destroy(string $encryptedId): RedirectResponse
    {
        $id = decryptId($encryptedId);

        abort_if(!$id, 404);

        $package = Package::findOrFail($id);

        try {
            DB::transaction(function () use ($package) {
                $package->delete();
            });

            return redirect()
                ->route('super_admin.packages.index')
                ->with('success', 'Paket berhasil dihapus.');
        } catch (Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Paket tidak dapat dihapus.');
        }
    }
}
