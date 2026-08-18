<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    // 1. Dashboard Utama Merchant
    public function dashboard(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $totalMenu = Menu::where('merchant_id', $merchantId)->count();
        $totalOrders = Order::where('merchant_id', $merchantId)->count();
        $todayOrders = Order::where('merchant_id', $merchantId)
            ->whereDate('created_at', today())
            ->count();

        $recentOrders = Order::with(['qrCode', 'items.menu'])
            ->where('merchant_id', $merchantId)
            ->latest()
            ->take(5)
            ->get();

        return view('merchant.dashboard', compact('totalMenu', 'totalOrders', 'todayOrders', 'recentOrders'));
    }

    // 2. Tampilan Kelola QR Code
    public function qrIndex(Request $request)
    {
        $merchantId = $request->user()->merchant_id;
        $qrCodes = QrCode::where('merchant_id', $merchantId)->get();

        return view('merchant.qr.index', compact('qrCodes'));
    }

    // 3. Simpan QR Code Baru
    public function qrStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $user = $request->user();

        if (!$user->merchant_id) {
            return back()->with('error', 'Akun kamu belum terhubung ke merchant mana pun.');
        }

        QrCode::create([
            'merchant_id' => $user->merchant_id,
            'name'        => $request->name,
            'type'        => $request->type,
            'code'        => Str::random(10),
            'status'      => 'active',
        ]);

        return back()->with('success', 'QR Code Meja berhasil dibuat!');
    }

    // 4. Hapus QR Code
    public function qrDestroy(Request $request, QrCode $qrCode)
    {
        if ($qrCode->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $qrCode->delete();

        return back()->with('success', 'QR Code Meja berhasil dihapus!');
    }

    // 5. Cetak QR Code Meja
    public function qrPrint(Request $request, QrCode $qrCode)
    {
        if ($qrCode->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        return view('merchant.qr.print', compact('qrCode'));
    }

    // 6. Update Status Pesanan
    public function updateOrderStatus(Request $request, Order $order)
    {
        if ($order->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:menunggu,diproses,selesai,dibatalkan',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // 7. Simpan Merchant Baru (Opsional - Pendaftaran Tenant)
    public function storeMerchant(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:8',
            'merchant_name' => 'required|string|max:255',
        ]);

        $merchant = Merchant::create([
            'name' => $request->merchant_name,
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'merchant_id' => $merchant->id,
            'role'        => 'owner',
        ]);

        return back()->with('success', 'Akun Merchant baru berhasil didaftarkan!');
    }

    // 8. Tampilan Edit Profil Kafe
    public function profileEdit(Request $request)
    {
        $merchant = $request->user()->merchant;
        return view('merchant.profile', compact('merchant'));
    }

    // 9. Simpan Perubahan Profil Kafe
    public function profileUpdate(Request $request)
    {
        $merchant = $request->user()->merchant;

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $merchant->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Profil kafe berhasil diperbarui!');
    }
}