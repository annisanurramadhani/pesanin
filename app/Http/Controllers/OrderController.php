<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItemUnit;
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
            ->with(['qrCode', 'items.menu', 'items.unit']);

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

            /*
            |--------------------------------------------------------------------------
            | DAPUR
            |--------------------------------------------------------------------------
            |
            | Hanya tampilkan pesanan yang:
            |
            | 1. Sudah dibayar
            | 2. Masih memiliki minimal 1 unit menu
            |    dengan status pending / processing
            |
            | Jika seluruh unit sudah completed atau cancelled,
            | pesanan otomatis hilang dari antrean dapur.
            |
            | Data order TIDAK dihapus dari database.
            |
            */

                    $query->where(
                        'payment_status',
                        'paid'
                    )
                        ->whereHas(
                            'items.unit',
                            function ($unitQuery) {

                                $unitQuery->whereIn(
                                    'status',
                                    [
                                        'pending',
                                        'processing',
                                    ]
                                );
                            }
                        );
                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | FILTER OWNER / KASIR
                    |--------------------------------------------------------------------------
                    |
                    | Order dengan pembayaran expired tidak ditampilkan.
                    | Berlaku untuk Kasir dan Owner.
                    |
                    */

                    $query->where(function ($q) {
                        $q->whereNull('payment_status')
                            ->orWhere('payment_status', '!=', 'expired');
                    });

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
                        ->orderBy('created_at', 'desc')
                        ->get();
                } else {

                    $orders = $query
                        ->orderBy('created_at', 'desc')
                        ->get();
                }


                /*
        |--------------------------------------------------------------------------
        | STATUS AGREGAT ORDER
        |--------------------------------------------------------------------------
        |
        | Status kasir dihitung langsung dari OrderItemUnit.
        | Tidak bergantung pada relasi $item->unit.
        |
        */

                if ($role !== 'dapur') {

                    $orders->each(function ($order) {

                        /*
                |--------------------------------------------------------------------------
                | AMBIL SEMUA ORDER ITEM ID
                |--------------------------------------------------------------------------
                */

                        $orderItemIds = $order->items
                            ->pluck('id');


                        /*
                |--------------------------------------------------------------------------
                | AMBIL SEMUA UNIT MENU
                |--------------------------------------------------------------------------
                */

                        $units = OrderItemUnit::whereIn(
                            'order_item_id',
                            $orderItemIds
                        )
                            ->get();


                        /*
                |--------------------------------------------------------------------------
                | HITUNG STATUS
                |--------------------------------------------------------------------------
                */

                        $totalUnits =
                            $units->count();

                        $completedUnits =
                            $units
                            ->where('status', 'completed')
                            ->count();

                        $cancelledUnits =
                            $units
                            ->where('status', 'cancelled')
                            ->count();

                        $processingUnits =
                            $units
                            ->where('status', 'processing')
                            ->count();

                        $pendingUnits =
                            $units
                            ->where('status', 'pending')
                            ->count();


                        /*
                |--------------------------------------------------------------------------
                | TENTUKAN STATUS ORDER
                |--------------------------------------------------------------------------
                */

                        if (
                            $totalUnits > 0 &&
                            $completedUnits === $totalUnits
                        ) {

                            $order->display_status =
                                'completed';
                        } elseif (
                            $totalUnits > 0 &&
                            $cancelledUnits === $totalUnits
                        ) {

                            $order->display_status =
                                'cancelled';
                        } elseif (
                            $cancelledUnits > 0
                        ) {

                            $order->display_status =
                                'partial_problem';
                        } elseif (
                            $processingUnits > 0
                        ) {

                            $order->display_status =
                                'processing';
                        } else {

                            $order->display_status =
                                'pending';
                        }


                        /*
                |--------------------------------------------------------------------------
                | SIMPAN RINGKASAN
                |--------------------------------------------------------------------------
                */

                        $order->status_summary = [

                            'total' =>
                            $totalUnits,

                            'completed' =>
                            $completedUnits,

                            'cancelled' =>
                            $cancelledUnits,

                            'processing' =>
                            $processingUnits,

                            'pending' =>
                            $pendingUnits,

                        ];
                    });
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

    /**
     * =========================================================
     * CHECK NEW ORDERS
     * =========================================================
     *
     * Digunakan oleh halaman Kasir dan Dapur dan Owner
     * untuk mengecek perubahan order secara berkala
     * tanpa reload halaman.
     */
    public function checkNew()
    {
        $user = Auth::user();

        $merchantId = $user->merchant_id ?? $user->id;

        /*
        |--------------------------------------------------------------------------
        | KASIR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'kasir') {

            $orders = Order::where(
                'merchant_id',
                $merchantId
            )
                ->where(
                    'payment_status',
                    'pending'
                )
                ->where(
                    'payment_method',
                    'cash'
                )
                ->orderByDesc(
                    'created_at'
                )
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders->map(function ($order) {

                    return [
                        'id' => $order->id,
                    ];

                })->values(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DAPUR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'dapur') {

            $orders = Order::where(
                'merchant_id',
                $merchantId
            )
                ->where(
                    'payment_status',
                    'paid'
                )
                ->whereHas(
                    'items.unit',
                    function ($query) {

                        $query->whereIn(
                            'status',
                            [
                                'pending',
                                'processing',
                            ]
                        );

                    }
                )
                ->orderByDesc(
                    'created_at'
                )
                ->get();

            return response()->json([
                'success' => true,
                'orders' => $orders->map(function ($order) {

                    return [
                        'id' => $order->id,
                    ];

                })->values(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | OWNER
        |--------------------------------------------------------------------------
        |
        | Owner membutuhkan perubahan status unit juga.
        |
        */

        if ($user->role === 'owner') {

            $orders = Order::where(
                'merchant_id',
                $merchantId
            )
                ->where(
                    'payment_status',
                    '!=',
                    'expired'
                )
                ->with([
                    'items.unit'
                ])
                ->orderByDesc(
                    'created_at'
                )
                ->get();


            return response()->json([
                'success' => true,

                'orders' => $orders->map(function ($order) {

                    $unitStatuses = [];

                    foreach ($order->items as $item) {

                        foreach ($item->unit ?? collect() as $unit) {

                            $unitStatuses[] = [
                                'id' => $unit->id,
                                'status' => $unit->status,
                            ];

                        }

                    }


                    return [
                        'id' => $order->id,

                        'payment_status' =>
                            $order->payment_status,

                        'status' =>
                            $order->status,

                        'units' =>
                            $unitStatuses,
                    ];

                })->values(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ROLE LAIN
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => false,
            'orders' => [],
        ]);
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
            'cashier_id' => Auth::id(),
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

    /*
|--------------------------------------------------------------------------
| DAPUR - UPDATE STATUS SATU UNIT MENU
|--------------------------------------------------------------------------
|
| Contoh:
|
| Nasi Goreng 1 → completed
|
| Tidak akan mengubah:
|
| Nasi Goreng 2
| Es Teh 1
| Ayam Bakar 1
|
*/

    public function updateUnitStatus(
        Request $request,
        $id
    ) {

        /*
    |--------------------------------------------------------------------------
    | VALIDASI STATUS
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'status' => [
                'required',
                'string',
                'in:pending,processing,completed,cancelled',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | USER & MERCHANT
    |--------------------------------------------------------------------------
    */

        $user = Auth::user();

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        /*
    |--------------------------------------------------------------------------
    | DECRYPT UNIT ID
    |--------------------------------------------------------------------------
    */

        $unitId =
            decryptId($id);

        abort_unless(
            $unitId,
            404
        );


        /*
    |--------------------------------------------------------------------------
    | AMBIL UNIT
    |--------------------------------------------------------------------------
    |
    | Sekaligus memastikan unit tersebut
    | benar-benar milik merchant yang sedang login.
    |
    */

        $unit = OrderItemUnit::where(
            'id',
            $unitId
        )
            ->whereHas(
                'orderItem.order',
                function ($query) use (
                    $merchantId
                ) {
                    $query->where(
                        'merchant_id',
                        $merchantId
                    );
                }
            )
            ->with([
                'orderItem.order',
            ])
            ->firstOrFail();


        /*
    |--------------------------------------------------------------------------
    | PASTIKAN PEMBAYARAN SUDAH DIBAYAR
    |--------------------------------------------------------------------------
    */

        $order =
            $unit->orderItem->order;

        if (
            $order->payment_status !== 'paid'
        ) {

            return back()->with(
                'error',
                'Pesanan belum dibayar dan belum dapat diproses dapur.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS UNIT
    |--------------------------------------------------------------------------
    */

        $unit->update([
            'status' =>
            $request->status,
        ]);


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'merchant.orders.index'
            )
            ->with(
                'success',
                'Status ' .
                    $unit->orderItem->menu_name .
                    ' ' .
                    $unit->unit_number .
                    ' berhasil diperbarui.'
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
