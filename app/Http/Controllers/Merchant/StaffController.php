<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\SecureText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Tampilkan daftar staf merchant.
     */
    public function index(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $staffs = User::where('merchant_id', $merchantId)
            ->whereNotIn('role', ['owner', 'super_admin'])
            ->latest()
            ->get();

        return view('merchant.staff.index', compact('staffs'));
    }

    /**
     * Simpan staf baru.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->merchant_id) {
            return back()->with(
                'error',
                'Akun kamu tidak memiliki Merchant ID.'
            );
        }

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    new SecureText,
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:6',
                ],

                'role' => [
                    'required',
                    'in:kasir,dapur',
                ],
            ],
            [
                'name.required' => 'Nama staf wajib diisi.',
                'name.string' => 'Nama staf harus berupa teks.',
                'name.max' => 'Nama staf maksimal 255 karakter.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email tersebut sudah digunakan.',

                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 6 karakter.',

                'role.required' => 'Role staf wajib dipilih.',
                'role.in' => 'Role hanya dapat berupa Kasir atau Dapur.',
            ]
        );

        User::create([
            'merchant_id' => $user->merchant_id,
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return back()->with(
            'success',
            'Akun staf berhasil ditambahkan!'
        );
    }

    /**
     * Tampilkan halaman edit staf.
     */
    public function edit(Request $request, string $encryptedId)
    {
        $staffId = decryptId($encryptedId);

        $staff = User::where('id', $staffId)
            ->where('merchant_id', $request->user()->merchant_id)
            ->whereIn('role', ['kasir', 'dapur'])
            ->firstOrFail();

        return view('merchant.staff.edit', compact('staff'));
    }

    /**
     * Update data staf.
     */
    public function update(Request $request, string $encryptedId)
    {
        $staffId = decryptId($encryptedId);

        $staff = User::where('id', $staffId)
            ->where('merchant_id', $request->user()->merchant_id)
            ->whereIn('role', ['kasir', 'dapur'])
            ->firstOrFail();

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    new SecureText,
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($staff->id),
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:6',
                ],

                'role' => [
                    'required',
                    'in:kasir,dapur',
                ],
            ],
            [
                'name.required' => 'Nama staf wajib diisi.',
                'name.string' => 'Nama staf harus berupa teks.',
                'name.max' => 'Nama staf maksimal 255 karakter.',

                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email tersebut sudah digunakan.',

                'password.min' => 'Password minimal 6 karakter.',

                'role.required' => 'Role staf wajib dipilih.',
                'role.in' => 'Role hanya dapat berupa Kasir atau Dapur.',
            ]
        );

        $staff->name = $validated['name'];
        $staff->email = strtolower($validated['email']);
        $staff->role = $validated['role'];

        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }

        $staff->save();

        return redirect()
            ->route('merchant.staff.index')
            ->with('success', 'Akun staf berhasil diperbarui!');
    }

    /**
     * Hapus staf.
     */
    public function destroy(Request $request, User $user)
    {
        if (
            $user->merchant_id !== $request->user()->merchant_id ||
            !in_array($user->role, ['kasir', 'dapur'])
        ) {
            abort(403);
        }

        $user->delete();

        return back()->with(
            'success',
            'Akun staf berhasil dihapus!'
        );
    }
}
