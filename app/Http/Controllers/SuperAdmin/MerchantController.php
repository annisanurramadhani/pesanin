<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use App\Models\Order;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
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

        return view('super_admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('super_admin.merchants.create');
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
            ->route('super_admin.merchants.index')
            ->with('success', 'Merchant berhasil ditambahkan.');
    }

    public function edit(string $encryptedId)
    {
        abort_unless(Auth::user()->role === 'super_admin', 403);

        try {
            $merchantId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $merchant = Merchant::findOrFail($merchantId);

        return view('super_admin.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, string $encryptedId)
    {
        abort_unless(Auth::user()->role === 'super_admin', 403);

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
            ->route('super_admin.merchants.index')
            ->with('success', 'Merchant berhasil diperbarui.');
    }

    public function destroy(string $encryptedId)
    {
        abort_unless(Auth::user()->role === 'super_admin', 403);

        try {
            $merchantId = Crypt::decryptString($encryptedId);
        } catch (\Throwable $e) {
            abort(404);
        }

        $merchant = Merchant::findOrFail($merchantId);

        $merchant->delete();

        return redirect()
            ->route('super_admin.merchants.index')
            ->with('success', 'Merchant berhasil dihapus.');
    }

    /**
     * Menampilkan halaman Dashboard Super Admin.
     */
    public function dashboard()
    {
        $totalMerchant = Merchant::count();
        $activeMerchant = Merchant::where('status', 'active')->count();
        $inactiveMerchant = Merchant::where('status', 'inactive')->count();
        $totalUsers = User::count();
        $totalOrder = Order::count();
        $pendingWithdrawal = Withdrawal::where('status', 'pending')->count();

        $recentWithdrawals = Withdrawal::with('merchant')
            ->latest()
            ->take(5)
            ->get();

        $recentMerchants = Merchant::latest()
            ->take(5)
            ->get();

        return view('super_admin.dashboard', compact(
            'totalMerchant',
            'activeMerchant',
            'inactiveMerchant',
            'totalUsers',
            'totalOrder',
            'pendingWithdrawal',
            'recentWithdrawals',
            'recentMerchants'
        ));
    }

    /**
     * Endpoint untuk mengirim data statistik terbaru secara JSON (Real-time update).
     */
    public function dashboardStats()
    {
        return response()->json([
            'total_merchant' => Merchant::count(),
            'merchant_active_count' => Merchant::where('status', 'active')->count(),
            'merchant_inactive_count' => Merchant::where('status', 'inactive')->count(),
            'total_users' => User::count(),
            'total_transaksi' => Order::count(),
            'penarikan_pending' => Withdrawal::where('status', 'pending')->count(),
        ]);
    }
}