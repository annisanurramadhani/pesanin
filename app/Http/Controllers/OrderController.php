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

    // 2. Update Status Pesanan (Pending -> Processing -> Completed / Canceled)
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        // Perbaikan: Enum disesuaikan dengan database (pending, processing, completed, canceled)
        $request->validate([
            'status' => 'required|in:pending,processing,completed,canceled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // 3. Polling Real-time Cek Pesanan Baru
    public function checkNew(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        $activeOrders = Order::where('merchant_id', $merchantId)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        return response()->json(['count' => $activeOrders]);
    }

    // 4. Cetak Struk / Nota Pembayaran
    public function receipt(Request $request, Order $order)
    {
        if ($order->merchant_id !== $request->user()->merchant_id) {
            abort(403);
        }

        $order->load(['items.menu', 'qrCode', 'merchant']);

        return view('merchant.orders.receipt', compact('order'));
    }
}