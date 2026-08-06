<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // 1. Tampilkan Daftar Pesanan Masuk untuk Merchant
    public function index(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $orders = Order::with(['qrCode', 'items.menu'])
            ->where('merchant_id', $merchantId)
            ->latest()
            ->paginate(10);

        return view('merchant.orders.index', compact('orders'));
    }

    // 2. Update Status Pesanan (Menunggu -> Diproses -> Selesai)
    public function updateStatus(Request $request, Order $order)
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

    public function checkNew(Request $request)
    {
        $merchantId = $request->user()->merchant_id;
        
        // Hitung total pesanan yang berstatus 'pending' (baru masuk) atau 'processing' (sedang dimasak)
        $activeOrders = Order::where('merchant_id', $merchantId)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        return response()->json(['count' => $activeOrders]);
    }
}