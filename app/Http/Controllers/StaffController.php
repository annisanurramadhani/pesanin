<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // 1. Tampilkan Daftar Staf
    public function index(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        // Ambil semua user di merchant ini SELAIN Owner & Super Admin
        $staffs = User::where('merchant_id', $merchantId)
            ->whereNotIn('role', ['owner', 'super_admin'])
            ->latest()
            ->get();

        return view('merchant.staff.index', compact('staffs'));
    }

    // 2. Simpan Staf Baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:kasir,dapur',
        ]);

        $user = $request->user();

        if (!$user->merchant_id) {
            return back()->with('error', 'Akun kamu tidak memiliki Merchant ID.');
        }

        User::create([
            'merchant_id' => $user->merchant_id,
            'name'        => $request->name,
            'email'       => strtolower($request->email),
            'password'    => \Illuminate\Support\Facades\Hash::make($request->password),
            'role'        => $request->role, // Simpan dalam format huruf kecil 'kasir' / 'dapur'
        ]);

        return back()->with('success', 'Akun staf berhasil ditambahkan!');
    }

    // 3. Hapus Staf
    public function destroy(Request $request, User $user)
    {
        if ($user->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $user->delete();

        return back()->with('success', 'Akun staf berhasil dihapus!');
    }
}