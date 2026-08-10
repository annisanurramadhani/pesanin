<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Support\Facades\Hash;


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

        \App\Models\QrCode::create([
            'merchant_id' => $user->merchant_id,
            'name'        => $request->name,
            'type'        => $request->type, // Menyimpan nilai: table, takeaway, atau vip
            'code_hash'   => \Illuminate\Support\Str::random(10),
            'is_active'   => true,
        ]);

        return back()->with('success', 'QR Code Meja berhasil dibuat!');
    }

    // Method Hapus QR Code
    public function qrDestroy(Request $request, \App\Models\QrCode $qrCode)
    {
        if ($qrCode->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $qrCode->delete();

        return back()->with('success', 'QR Code Meja berhasil dihapus!');
    }

    public function qrPrint(Request $request, QrCode $qrCode)
    {
        // Pastikan QR code milik merchant yang sedang login
        if ($qrCode->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        return view('merchant.qr.print', compact('qrCode'));
    }

    // 4. Update Status Pesanan
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

    public function storeMerchant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'merchant_name' => 'required|string|max:255',
        ]);

        // 1. Buat Data Merchant/Kafe
        $merchant = Merchant::create([
            'name' => $request->merchant_name,
        ]);

        // 2. Buat Akun User untuk Merchant tersebut
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'merchant_id' => $merchant->id,
        ]);

        return back()->with('success', 'Akun Merchant baru berhasil didaftarkan!');
    }
}