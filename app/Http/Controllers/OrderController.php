<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\OrderReceiptMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $query = Order::where('merchant_id', $merchantId)
            ->with(['qrCode', 'items.menu']);

        // Default variabel untuk compact view
        $filterType    = $request->get('filter_type', 'day');
        $selectedDate  = $request->get('date', Carbon::today()->toDateString());
        $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
        $selectedYear  = $request->get('year', Carbon::now()->year);
        $labelPeriode  = Carbon::today()->format('d M Y');

        // LOGIKA PERBEDAAN ROLE (KASIR VS OWNER)
        if ($user->role === 'kasir') {
            // KASIR: Hanya melihat pesanan HARI INI
            $query->whereDate('created_at', Carbon::today());
            $labelPeriode = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
        } else {
            // OWNER: Filter fleksibel (Per Hari, Per Bulan, Per Tahun)
            if ($filterType === 'day') {
                $query->whereDate('created_at', $selectedDate);
                $labelPeriode = Carbon::parse($selectedDate)->format('d M Y');
            } elseif ($filterType === 'month') {
                $carbonMonth = Carbon::parse($selectedMonth);
                $query->whereYear('created_at', $carbonMonth->year)
                      ->whereMonth('created_at', $carbonMonth->month);
                $labelPeriode = $carbonMonth->format('F Y');
            } elseif ($filterType === 'year') {
                $query->whereYear('created_at', $selectedYear);
                $labelPeriode = 'Tahun ' . $selectedYear;
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Hitung Total Pendapatan
        $totalRevenue = $orders->sum(function($order) {
            if ($order->total_amount > 0) return $order->total_amount;
            if ($order->total_price > 0) return $order->total_price;

            return $order->items->sum(function($item) {
                return $item->subtotal ?? ($item->price * $item->quantity);
            });
        });

        $totalOrders = $orders->count();

        return view('merchant.orders.index', compact(
            'orders',
            'filterType',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'labelPeriode',
            'totalRevenue',
            'totalOrders'
        ));
    }

    // Method untuk Kasir Memperbarui / Memvalidasi Status Pesanan
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
        'status' => 'required|string',
        ]);

        $orderId = decryptId($id);

        abort_unless($orderId, 404);

        $order = Order::findOrFail($orderId);

        $order->status = $request->status;
        $order->save();

        return back()->with(
            'success',
            'Status pesanan #' . $order->order_number . ' berhasil diperbarui!');
    }


    public function receipt($id)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $orderId = decryptId($id);

        abort_unless($orderId, 404);

        $order = Order::where('merchant_id', $merchantId)
            ->with([
                'merchant',
                'qrCode',
                'items.menu',
            ])
            ->findOrFail($orderId);

        return view('merchant.orders.receipt', compact('order'));
    }

    public function sendReceipt($id)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $orderId = decryptId($id);

        abort_unless($orderId, 404);

        $order = Order::where('merchant_id', $merchantId)
            ->with([
                'merchant',
                'qrCode',
                'items.menu',
            ])
            ->findOrFail($orderId);

        if (!$order->customer_email) {
            return back()->with(
                'error',
                'Email pelanggan belum tersedia.'
            );
        }

        Mail::to($order->customer_email)
            ->send(new OrderReceiptMail($order));

        $order->update([
            'receipt_sent_at' => now(),
        ]);

        return back()->with(
            'success',
            'Struk berhasil dikirim ke email pelanggan.'
        );
    }

}
