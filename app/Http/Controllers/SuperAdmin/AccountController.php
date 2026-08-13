<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use App\Rules\SecureText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Menampilkan seluruh akun.
     */
    public function index()
    {
        $users = User::with('merchant')
            ->latest()
            ->paginate(10);

        return view('super_admin.accounts.index', compact('users'));
    }

    /**
     * Form tambah akun.
     */
    public function create()
    {
        $merchants = Merchant::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'super_admin.accounts.create',
            compact('merchants')
        );
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                new SecureText,
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'owner',
                    'kasir',
                    'dapur',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ], [
            'merchant_id.required' => 'Merchant wajib dipilih.',
            'merchant_id.exists' => 'Merchant yang dipilih tidak ditemukan.',

            'name.required' => 'Nama akun wajib diisi.',
            'name.string' => 'Nama akun harus berupa teks.',
            'name.max' => 'Nama akun maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role akun tidak valid.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status akun tidak valid.',
        ]);

        $user = User::create([
            'merchant_id' => $validated['merchant_id'],
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('super_admin.accounts.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Form edit akun.
     */
    public function edit(string $encryptedId)
    {
        try {
            $userId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $user = User::findOrFail($userId);

        // Super Admin tidak boleh diedit melalui menu ini.
        if ($user->role === 'super_admin') {
            abort(403);
        }

        $merchants = Merchant::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'super_admin.accounts.edit',
            compact(
                'user',
                'merchants'
            )
        );
    }

    /**
     * Update akun.
     */
    public function update(Request $request, string $encryptedId)
    {
        // =====================================================
        // DECRYPT ID
        // =====================================================
        try {
            $userId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $user = User::findOrFail($userId);

        // Super Admin tidak boleh diedit
        if ($user->role === 'super_admin') {
            abort(403);
        }

        // =====================================================
        // VALIDATION
        // =====================================================
        $validated = $request->validate([
            'merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                new SecureText,
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                Rule::in([
                    'owner',
                    'kasir',
                    'dapur',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ], [
            'merchant_id.required' => 'Merchant wajib dipilih.',
            'merchant_id.exists' => 'Merchant yang dipilih tidak ditemukan.',

            'name.required' => 'Nama akun wajib diisi.',
            'name.string' => 'Nama akun harus berupa teks.',
            'name.max' => 'Nama akun maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan akun lain.',

            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',

            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role akun tidak valid.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status akun tidak valid.',
        ]);

        // =====================================================
        // NORMALISASI DATA
        // =====================================================
        $newEmail = strtolower(trim($validated['email']));
        $oldEmail = strtolower(trim($user->email));

        // Cek apakah email benar-benar berubah
        $emailChanged = $oldEmail !== $newEmail;

        // =====================================================
        // DATA YANG DIUPDATE
        // =====================================================
        $data = [
            'merchant_id' => $validated['merchant_id'],
            'name' => trim($validated['name']),
            'email' => $newEmail,
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        // Password hanya diubah jika diisi
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // =====================================================
        // UPDATE DATA AKUN
        // =====================================================
        $user->update($data);

        // =====================================================
        // JIKA EMAIL BERUBAH
        // =====================================================
        if ($emailChanged) {

            /*
            |--------------------------------------------------------------------------
            | Reset status verifikasi email
            |--------------------------------------------------------------------------
            */

            $user->forceFill([
                'email_verified_at' => null,
            ]);

            $user->save();

            /*
            |--------------------------------------------------------------------------
            | Refresh data user
            |--------------------------------------------------------------------------
            */

            $user->refresh();

            /*
            |--------------------------------------------------------------------------
            | Kirim email verifikasi ke email BARU
            |--------------------------------------------------------------------------
            */

            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('super_admin.accounts.index')
                ->with(
                    'success',
                    'Akun berhasil diperbarui. Link verifikasi telah dikirim ke email baru.'
                );
        }

        // =====================================================
        // EMAIL TIDAK BERUBAH
        // =====================================================

        return redirect()
            ->route('super_admin.accounts.index')
            ->with(
                'success',
                'Akun berhasil diperbarui.'
            );
    }

    /**
     * Hapus akun.
     */
    public function destroy(string $encryptedId)
    {
        try {
            $userId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $user = User::findOrFail($userId);

        // Super Admin tidak boleh dihapus.
        if ($user->role === 'super_admin') {
            abort(403);
        }

        // Tidak boleh menghapus akun yang sedang login.
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('super_admin.accounts.index')
                ->with(
                    'error',
                    'Akun yang sedang digunakan tidak dapat dihapus.'
                );
        }

        $user->delete();

        return redirect()
            ->route('super_admin.accounts.index')
            ->with(
                'success',
                'Akun berhasil dihapus.'
            );
    }
}
