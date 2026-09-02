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

        /*
        |--------------------------------------------------------------------------
        | ROLE
        |--------------------------------------------------------------------------
        */

        $role = $user->role;


        /*
        |--------------------------------------------------------------------------
        | DEFAULT FILTER
        |--------------------------------------------------------------------------
        */

        $filterType    = $request->get('filter_type', 'day');
        $selectedDate  = $request->get(
            'date',
            Carbon::today()->toDateString()
        );
        $selectedMonth = $request->get(
            'month',
            Carbon::now()->format('Y-m')
        );
        $selectedYear  = $request->get(
            'year',
            Carbon::now()->year
        );

        $labelPeriode = Carbon::today()->format('d M Y');


        /*
        |--------------------------------------------------------------------------
        | FILTER KHUSUS DAPUR
        |--------------------------------------------------------------------------
        |
        | Dapur hanya melihat pesanan yang SUDAH DIBAYAR.
        |
        | Cash yang belum dibayar:
        | payment_status = pending
        |
        | Tidak akan masuk dapur.
        |
        */

        if ($role === 'dapur') {

            $query->where('payment_status', 'paid')
                ->whereIn('status', [
                    'pending',
                    'processing',
                ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | FILTER OWNER / KASIR
            |--------------------------------------------------------------------------
            */

            if ($filterType === 'day') {

                $query->whereDate(
                    'created_at',
                    $selectedDate
                );

                $labelPeriode = Carbon::parse(
                    $selectedDate
                )->format('d M Y');

            } elseif ($filterType === 'month') {

                $carbonMonth = Carbon::parse(
                    $selectedMonth
                );

                $query->whereYear(
                    'created_at',
                    $carbonMonth->year
                )->whereMonth(
                    'created_at',
                    $carbonMonth->month
                );

                $labelPeriode = $carbonMonth->format(
                    'F Y'
                );

            } elseif ($filterType === 'year') {

                $query->whereYear(
                    'created_at',
                    $selectedYear
                );

                $labelPeriode =
                    'Tahun ' . $selectedYear;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL ORDER
        |--------------------------------------------------------------------------
        |
        | Dapur:
        | oldest first → sistem antrean dapur.
        |
        | Kasir / Owner:
        | newest first.
        |
        */

        if ($role === 'dapur') {

            $orders = $query
                ->orderBy('created_at', 'asc')
                ->get();

        } else {

            $orders = $query
                ->orderBy('created_at', 'desc')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG TOTAL PENDAPATAN
        |--------------------------------------------------------------------------
        |
        | Hanya pembayaran yang SUDAH PAID
        | yang dihitung sebagai pendapatan.
        |
        */

        $totalRevenue = $orders
            ->where('payment_status', 'paid')
            ->sum(function ($order) {

                if (
                    isset($order->total) &&
                    (float) $order->total > 0
                ) {
                    return (float) $order->total;
                }

                return $order->items->sum(function ($item) {

                    return $item->subtotal
                        ?? (
                            $item->price *
                            $item->quantity
                        );
                });
            });


        /*
        |--------------------------------------------------------------------------
        | TOTAL ORDER
        |--------------------------------------------------------------------------
        */

        $totalOrders = $orders->count();


        return view(
            'merchant.orders.index',
            compact(
                'orders',
                'filterType',
                'selectedDate',
                'selectedMonth',
                'selectedYear',
                'labelPeriode',
                'totalRevenue',
                'totalOrders'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KASIR - KONFIRMASI PEMBAYARAN CASH
    |--------------------------------------------------------------------------
    |
    | Method ini HANYA mengubah payment_status.
    |
    | Tidak menyentuh status makanan.
    |
    */

    public function markAsPaid($id)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $orderId = decryptId($id);

        abort_unless($orderId, 404);

        $order = Order::where(
            'merchant_id',
            $merchantId
        )->findOrFail($orderId);


        /*
        |--------------------------------------------------------------------------
        | Pastikan hanya order CASH
        |--------------------------------------------------------------------------
        */

        if ($order->payment_method !== 'cash') {

            return back()->with(
                'error',
                'Pesanan ini bukan pembayaran tunai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan belum dibayar
        |--------------------------------------------------------------------------
        */

        if ($order->payment_status === 'paid') {

            return back()->with(
                'error',
                'Pesanan ini sudah dibayar.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Konfirmasi pembayaran
        |--------------------------------------------------------------------------
        */

        $order->update([
            'payment_status' => 'paid',
        ]);


        return redirect()
            ->route('merchant.orders.index')
            ->with(
                'success',
                'Pembayaran pesanan #' .
                $order->order_number .
                ' berhasil dikonfirmasi.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DAPUR - UPDATE STATUS MAKANAN
    |--------------------------------------------------------------------------
    |
    | Status pembayaran TIDAK disentuh di sini.
    |
    */

    public function updateStatus(
        Request $request,
        $id
    ) {
        $request->validate([
            'status' => [
                'required',
                'string',
                'in:pending,processing,completed,cancelled',
            ],
        ]);


        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $orderId = decryptId($id);

        abort_unless($orderId, 404);


        /*
        |--------------------------------------------------------------------------
        | Ambil order berdasarkan merchant
        |--------------------------------------------------------------------------
        */

        $order = Order::where(
            'merchant_id',
            $merchantId
        )->findOrFail($orderId);


        /*
        |--------------------------------------------------------------------------
        | Dapur hanya boleh memproses order yang sudah dibayar
        |--------------------------------------------------------------------------
        */

        if ($order->payment_status !== 'paid') {

            return back()->with(
                'error',
                'Pesanan belum dibayar dan belum dapat diproses dapur.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Update STATUS MAKANAN
        |--------------------------------------------------------------------------
        */

        $order->update([
            'status' => $request->status,
        ]);


        return redirect()
            ->route('merchant.orders.index')
            ->with(
                'success',
                'Status pesanan #' .
                $order->order_number .
                ' berhasil diperbarui!'
            );
    }


    public function receipt($id)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $orderId = decryptId($id);

        abort_unless($orderId, 404);

        $order = Order::where(
            'merchant_id',
            $merchantId
        )
            ->with([
                'merchant',
                'qrCode',
                'items.menu',
            ])
            ->findOrFail($orderId);

        return view(
            'merchant.orders.receipt',
            compact('order')
        );
    }


    public function sendReceipt($id)
    {
        $user = Auth::user();
        $merchantId = $user->merchant_id ?? $user->id;

        $orderId = decryptId($id);

        abort_unless($orderId, 404);

        $order = Order::where(
            'merchant_id',
            $merchantId
        )
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
            ->send(
                new OrderReceiptMail($order)
            );

        $order->update([
            'receipt_sent_at' => now(),
        ]);

        return back()->with(
            'success',
            'Struk berhasil dikirim ke email pelanggan.'
        );
    }
}