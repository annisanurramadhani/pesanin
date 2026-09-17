<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\OrderReceiptMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WalletService;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = Auth::user();

        $merchantId = $user->merchant_id ?? $user->id;

        $query = Order::where(
            'merchant_id',
            $merchantId
        )->with([
            'qrCode',
            'items.menu',
            'items.unit',
        ]);


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

        $filterType = $request->get(
            'filter_type',
            'day'
        );

        $selectedDate = $request->get(
            'date',
            Carbon::today()->toDateString()
        );

        $selectedMonth = $request->get(
            'month',
            Carbon::now()->format('Y-m')
        );

        $selectedYear = $request->get(
            'year',
            Carbon::now()->year
        );

        $labelPeriode =
            Carbon::today()->format('d M Y');


        /*
        |--------------------------------------------------------------------------
        | DAPUR
        |--------------------------------------------------------------------------
        |
        | Dapur hanya melihat:
        |
        | payment_status = paid
        |
        | dan masih memiliki unit:
        |
        | pending / processing
        |
        */

        if ($role === 'dapur') {

            $query
                ->where(
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
            | KASIR / OWNER
            |--------------------------------------------------------------------------
            |
            | Order expired tidak ditampilkan.
            |
            */

            $query->where(function ($q) {

                $q->whereNull(
                    'payment_status'
                )->orWhere(
                    'payment_status',
                    '!=',
                    'expired'
                );
            });


            /*
            |--------------------------------------------------------------------------
            | FILTER PERIODE
            |--------------------------------------------------------------------------
            */

            if ($filterType === 'day') {

                $query->whereDate(
                    'created_at',
                    $selectedDate
                );

                $labelPeriode =
                    Carbon::parse(
                        $selectedDate
                    )->format('d M Y');
            } elseif ($filterType === 'month') {

                $carbonMonth =
                    Carbon::parse(
                        $selectedMonth
                    );

                $query
                    ->whereYear(
                        'created_at',
                        $carbonMonth->year
                    )
                    ->whereMonth(
                        'created_at',
                        $carbonMonth->month
                    );

                $labelPeriode =
                    $carbonMonth->format('F Y');
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
        */

        $orders = $query
            ->orderBy(
                'created_at',
                'desc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | HITUNG STATUS AGREGAT
        |--------------------------------------------------------------------------
        |
        | Khusus Kasir / Owner.
        |
        | Status diambil langsung dari
        | order_item_units.
        |
        */

        if ($role !== 'dapur') {

            $orders->each(
                function ($order) {

                    $orderItemIds =
                        $order->items->pluck('id');


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL UNIT
                    |--------------------------------------------------------------------------
                    */

                    $units =
                        OrderItemUnit::whereIn(
                            'order_item_id',
                            $orderItemIds
                        )->get();


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG STATUS UNIT
                    |--------------------------------------------------------------------------
                    */

                    $totalUnits =
                        $units->count();

                    $completedUnits =
                        $units
                        ->where(
                            'status',
                            'completed'
                        )
                        ->count();

                    $cancelledUnits =
                        $units
                        ->where(
                            'status',
                            'cancelled'
                        )
                        ->count();

                    $processingUnits =
                        $units
                        ->where(
                            'status',
                            'processing'
                        )
                        ->count();

                    $pendingUnits =
                        $units
                        ->where(
                            'status',
                            'pending'
                        )
                        ->count();


                    /*
                    |--------------------------------------------------------------------------
                    | TENTUKAN DISPLAY STATUS
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
                    | STATUS SUMMARY
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
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | TOTAL PENDAPATAN
        |--------------------------------------------------------------------------
        */

        $totalRevenue =
            $orders
            ->where(
                'payment_status',
                'paid'
            )
            ->sum(
                function ($order) {

                    if (
                        isset($order->total) &&
                        (float) $order->total > 0
                    ) {

                        return (float)
                        $order->total;
                    }

                    return $order->items->sum(
                        function ($item) {

                            return
                                $item->subtotal
                                ??
                                (
                                    $item->price *
                                    $item->quantity
                                );
                        }
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | TOTAL ORDER
        |--------------------------------------------------------------------------
        */

        $totalOrders =
            $orders->count();


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

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
    | CHECK NEW ORDERS
    |--------------------------------------------------------------------------
    |
    | Dipakai oleh Kasir, Dapur, dan Owner.
    |
    */

    public function checkNew()
    {
        $user = Auth::user();

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        /*
        |--------------------------------------------------------------------------
        | KASIR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'kasir') {

            $orders =
                Order::where(
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

                'orders' =>
                $orders
                    ->map(
                        function ($order) {

                            return [
                                'id' =>
                                $order->id,
                            ];
                        }
                    )
                    ->values(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DAPUR
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'dapur') {

            $orders =
                Order::where(
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

                'orders' =>
                $orders
                    ->map(
                        function ($order) {

                            return [
                                'id' =>
                                $order->id,
                            ];
                        }
                    )
                    ->values(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | OWNER
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'owner') {

            $orders =
                Order::where(
                    'merchant_id',
                    $merchantId
                )
                ->where(
                    'payment_status',
                    '!=',
                    'expired'
                )
                ->with([
                    'items.unit',
                ])
                ->orderByDesc(
                    'created_at'
                )
                ->get();


            return response()->json([

                'success' => true,

                'orders' =>
                $orders
                    ->map(
                        function ($order) {

                            $unitStatuses = [];


                            foreach (
                                $order->items
                                as $item
                            ) {

                                foreach (
                                    $item->unit
                                        ?? collect()
                                    as $unit
                                ) {

                                    $unitStatuses[] = [

                                        'id' =>
                                        $unit->id,

                                        'status' =>
                                        $unit->status,

                                    ];
                                }
                            }


                            return [

                                'id' =>
                                $order->id,

                                'payment_status' =>
                                $order->payment_status,

                                'status' =>
                                $order->status,

                                'units' =>
                                $unitStatuses,

                            ];
                        }
                    )
                    ->values(),

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
    */

    public function markAsPaid(
        $id,
        \App\Services\WalletService $walletService
    )
    {
        $user = Auth::user();

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        $orderId =
            decryptId($id);


        abort_unless(
            $orderId,
            404
        );


        /*
        |--------------------------------------------------------------------------
        | AMBIL ORDER
        |--------------------------------------------------------------------------
        */

        $order =
            Order::where(
                'merchant_id',
                $merchantId
            )
            ->findOrFail(
                $orderId
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI PAYMENT METHOD
        |--------------------------------------------------------------------------
        */

        if (
            strtolower(
                $order->payment_method
                    ?? ''
            ) !== 'cash'
        ) {

            return back()->with(
                'error',
                'Pesanan ini bukan pembayaran tunai.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI PAYMENT STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $order->payment_status ===
            'paid'
        ) {

            return back()->with(
                'error',
                'Pesanan ini sudah dibayar.'
            );
        }
        $request->validate([
            'cash_received' => [
                'required',
                'numeric',
                'min:' . $order->total
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE PAYMENT
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $order,
                $request,
                $walletService
            ) {


                $order->cash_received =
                    $request->cash_received;


                $order->cash_change =
                    $request->cash_received
                    - $order->total;



                $order->payment_status =
                    'paid';



                $order->updated_at =
                    now();



                $order->save();
                /*
                |--------------------------------------------------------------------------
                | Tambahkan saldo merchant
                |--------------------------------------------------------------------------
                */

                $walletService
                    ->addOrderPayment(
                        $order
                    );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'merchant.orders.index'
            )
            ->with(
                'success',
                'Pembayaran pesanan #' .
                    $order->order_number .
                    ' berhasil dikonfirmasi.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DAPUR - UPDATE STATUS ORDER
    |--------------------------------------------------------------------------
    |
    | Method ini dipertahankan untuk kebutuhan
    | update status order secara umum.
    |
    | Untuk aksi per menu/unit,
    | gunakan updateUnitStatus().
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

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        $orderId =
            decryptId($id);


        abort_unless(
            $orderId,
            404
        );


        $order =
            Order::where(
                'merchant_id',
                $merchantId
            )
            ->findOrFail(
                $orderId
            );


        /*
        |--------------------------------------------------------------------------
        | HARUS SUDAH DIBAYAR
        |--------------------------------------------------------------------------
        */

        if (
            $order->payment_status !==
            'paid'
        ) {

            return back()->with(
                'error',
                'Pesanan belum dibayar dan belum dapat diproses dapur.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE ORDER
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $order,
                $request
            ) {

                $order->status =
                    $request->status;

                $order->updated_at =
                    now();

                $order->save();


                /*
                |--------------------------------------------------------------------------
                | Jika update status order selesai,
                | sinkronkan unit yang masih aktif.
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $request->status,
                        [
                            'completed',
                            'cancelled',
                        ],
                        true
                    )
                ) {

                    $orderItemIds =
                        $order->items()
                        ->pluck('id');


                    OrderItemUnit::whereIn(
                        'order_item_id',
                        $orderItemIds
                    )
                        ->whereIn(
                            'status',
                            [
                                'pending',
                                'processing',
                            ]
                        )
                        ->update([

                            'status' =>
                            $request->status,

                            'updated_at' =>
                            now(),

                        ]);
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'merchant.orders.index'
            )
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
    | INI YANG PALING PENTING UNTUK REALTIME DASHBOARD.
    |
    | Contoh:
    |
    | Hamburger 1 → completed
    |
    | Hamburger 2 → processing
    |
    | Setiap unit diproses sendiri.
    |
    */

    public function updateUnitStatus(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | VALIDASI
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
        | USER
        |--------------------------------------------------------------------------
        */

        $user =
            Auth::user();


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
        | Sekaligus memastikan unit
        | milik merchant yang sedang login.
        |
        */

        $unit =
            OrderItemUnit::where(
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
        | AMBIL ORDER
        |--------------------------------------------------------------------------
        */

        $order =
            $unit
            ->orderItem
            ->order;


        /*
        |--------------------------------------------------------------------------
        | VALIDASI PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        if (
            $order->payment_status !==
            'paid'
        ) {

            return back()->with(
                'error',
                'Pesanan belum dibayar dan belum dapat diproses dapur.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE UNIT + TOUCH ORDER
        |--------------------------------------------------------------------------
        |
        | Ini bagian penting untuk dashboard realtime.
        |
        */

        DB::transaction(
            function () use (
                $unit,
                $order,
                $request
            ) {

                /*
                |--------------------------------------------------------------------------
                | Update unit makanan
                |--------------------------------------------------------------------------
                */

                $unit->status =
                    $request->status;

                $unit->updated_at =
                    now();

                $unit->save();


                /*
                |--------------------------------------------------------------------------
                | Update timestamp order induk
                |--------------------------------------------------------------------------
                |
                | Dashboard menggunakan updated_at
                | untuk menentukan order terbaru.
                |
                */

                $order->updated_at =
                    now();

                $order->save();
            }
        );


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


    /*
    |--------------------------------------------------------------------------
    | RECEIPT
    |--------------------------------------------------------------------------
    */

    public function receipt($id)
    {
        $user =
            Auth::user();

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        $orderId =
            decryptId($id);


        abort_unless(
            $orderId,
            404
        );


        $order =
            Order::where(
                'merchant_id',
                $merchantId
            )
            ->with([
                'merchant',
                'qrCode',
                'items.menu',
            ])
            ->findOrFail(
                $orderId
            );


        return view(
            'merchant.orders.receipt',
            compact('order')
        );
    }

    //buat cetak pdf struk
    public function receiptPdf($id)
    {
        $user = Auth::user();

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        $orderId = decryptId($id);


        abort_unless(
            $orderId,
            404
        );


        $order = Order::where(
            'merchant_id',
            $merchantId
        )
            ->with([
                'merchant.settings',
                'qrCode',
                'items.menu',
                'cashier',
            ])
            ->findOrFail($orderId);



        $pdf = Pdf::loadView(
            'merchant.orders.receipt-pdf',
            compact('order')
        );


        /*
        |--------------------------------------------------------------------------
        | Ukuran Thermal Printer
        |--------------------------------------------------------------------------
        |
        | 58mm
        |
        */

        $height = 300 +
            ($order->items->count() * 40);


        $pdf->setPaper(
            [
                0,
                0,
                164.40, // 58mm
                $height
            ],
            'portrait'
        );


        return $pdf->stream(
            'receipt-' . $order->order_number . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SEND RECEIPT
    |--------------------------------------------------------------------------
    */

    public function sendReceipt($id)
    {
        $user =
            Auth::user();

        $merchantId =
            $user->merchant_id
            ?? $user->id;


        $orderId =
            decryptId($id);


        abort_unless(
            $orderId,
            404
        );


        $order =
            Order::where(
                'merchant_id',
                $merchantId
            )
            ->with([
                'merchant',
                'qrCode',
                'items.menu',
            ])
            ->findOrFail(
                $orderId
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDASI EMAIL
        |--------------------------------------------------------------------------
        */

        if (!$order->customer_email) {

            return back()->with(
                'error',
                'Email pelanggan belum tersedia.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | KIRIM EMAIL
        |--------------------------------------------------------------------------
        */

        Mail::to(
            $order->customer_email
        )->send(
            new OrderReceiptMail(
                $order
            )
        );


        /*
        |--------------------------------------------------------------------------
        | SIMPAN WAKTU STRUK
        |--------------------------------------------------------------------------
        */

        $order->update([

            'receipt_sent_at' =>
            now(),

        ]);


        return back()->with(
            'success',
            'Struk berhasil dikirim ke email pelanggan.'
        );
    }
}
