@extends('layouts.merchant')

@section('header')

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    <div>

        <h2 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900">
            Dashboard Merchant
        </h2>

        <p class="mt-1 text-xs font-medium text-slate-500">
            Pantau aktivitas penjualan, pesanan masuk, dan performa kafemu secara real-time.
        </p>

    </div>


    <div class="flex items-center gap-3">

        <div
            class="flex items-center gap-2 rounded-xl border border-emerald-200/60 bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-600"
        >

            <span class="h-2 w-2 animate-ping rounded-full bg-emerald-500"></span>

            Sistem Pesanan On

        </div>

    </div>

</div>

@endsection


@section('content')

<div class="space-y-8">


    {{-- ================================================================
         STAT CARDS
    ================================================================= --}}

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">


        {{-- Total Menu --}}

        <div
            class="group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md"
        >

            <div
                class="absolute -bottom-4 -right-4 h-24 w-24 rounded-full bg-amber-500/10 transition duration-300 group-hover:scale-125"
            ></div>

            <div class="relative z-10 space-y-1">

                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Total Menu Aktif
                </p>

                <h3 class="text-4xl font-black tracking-tight text-slate-900">
                    {{ $totalMenu ?? 0 }}
                </h3>

                <p class="pt-1 text-xs font-medium text-slate-500">
                    Produk siap dipesan
                </p>

            </div>


            <div
                class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500 text-xl font-black text-slate-950 shadow-lg shadow-amber-500/20"
            >

                <i class="fa-solid fa-utensils"></i>

            </div>

        </div>


        {{-- Pesanan Hari Ini --}}

        <div
            class="group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md"
        >

            <div
                class="absolute -bottom-4 -right-4 h-24 w-24 rounded-full bg-emerald-500/10 transition duration-300 group-hover:scale-125"
            ></div>

            <div class="relative z-10 space-y-1">

                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Pesanan Hari Ini
                </p>

                <h3 class="text-4xl font-black tracking-tight text-slate-900">
                    {{ $todayOrders ?? 0 }}
                </h3>

                <p class="flex items-center gap-1 pt-1 text-xs font-bold text-emerald-600">

                    <i class="fa-solid fa-arrow-trend-up"></i>

                    Transaksi baru masuk

                </p>

            </div>


            <div
                class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500 text-xl font-black text-white shadow-lg shadow-emerald-500/20"
            >

                <i class="fa-solid fa-bag-shopping"></i>

            </div>

        </div>


        {{-- Total Transaksi --}}

        <div
            class="group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md"
        >

            <div
                class="absolute -bottom-4 -right-4 h-24 w-24 rounded-full bg-blue-500/10 transition duration-300 group-hover:scale-125"
            ></div>

            <div class="relative z-10 space-y-1">

                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Total Keseluruhan
                </p>

                <h3 class="text-4xl font-black tracking-tight text-slate-900">
                    {{ $totalOrders ?? 0 }}
                </h3>

                <p class="pt-1 text-xs font-medium text-slate-500">
                    Riwayat semua order
                </p>

            </div>


            <div
                class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-xl font-black text-white shadow-lg shadow-blue-500/20"
            >

                <i class="fa-solid fa-receipt"></i>

            </div>

        </div>

    </div>


    {{-- ================================================================
         TABEL PESANAN TERBARU
    ================================================================= --}}

    <div
        class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm"
    >

        <div class="flex items-center justify-between border-b border-slate-100 p-6">

            <div>

                <h3 class="text-lg font-extrabold text-slate-900">
                    Pesanan Terbaru Masuk
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    5 transaksi paling akhir dari pelanggan meja
                </p>

            </div>


            <a
                href="{{ route('merchant.orders.index') }}"
                class="flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 hover:shadow"
            >

                <span>
                    Lihat Semua Orders
                </span>

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left text-sm">

                <thead
                    class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-400"
                >

                    <tr>

                        <th class="p-4 pl-6">
                            No. Order & Meja
                        </th>

                        <th class="p-4">
                            Pelanggan
                        </th>

                        <th class="p-4">
                            Pesanan
                        </th>

                        <th class="p-4">
                            Total Harga
                        </th>

                        <th class="p-4 pr-6">
                            Status Pesanan
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($recentOrders ?? [] as $order)

                        @php

                            $itemTotal = $order->items->sum(function ($item) {

                                return $item->subtotal
                                    ?? (
                                        $item->price
                                        * $item->quantity
                                    );

                            });


                            $grandTotal =
                                ($order->total_amount > 0)
                                    ? $order->total_amount
                                    : (
                                        ($order->total_price > 0)
                                            ? $order->total_price
                                            : $itemTotal
                                    );


                            $statusStr = strtolower(
                                $order->status ?? ''
                            );

                        @endphp


                        <tr class="transition hover:bg-slate-50/60">


                            {{-- No Order & Meja --}}

                            <td class="p-4 pl-6">

                                <span class="block text-base font-black text-slate-900">
                                    #{{ $order->order_number }}
                                </span>

                                <span
                                    class="mt-1 inline-flex items-center gap-1 rounded-md border border-amber-200/60 bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-600"
                                >

                                    <i class="fa-solid fa-location-dot"></i>

                                    {{ $order->qrCode->name ?? 'Meja General' }}

                                </span>

                            </td>


                            {{-- Pelanggan --}}

                            <td class="p-4">

                                <p class="font-bold text-slate-800">
                                    {{ $order->customer_name }}
                                </p>

                                <p class="mt-0.5 text-[11px] font-medium text-slate-400">

                                    <i class="fa-regular fa-clock mr-0.5 text-[10px]"></i>

                                    {{ $order->created_at->format('d M Y, H:i') }} WIB

                                </p>

                            </td>


                            {{-- Detail Item --}}

                            <td class="p-4">

                                <div class="flex flex-wrap gap-1">

                                    @foreach($order->items as $item)

                                        <span
                                            class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700"
                                        >

                                            {{ $item->menu_name ?? $item->menu->name ?? 'Menu' }}

                                            <b class="font-black text-slate-900">
                                                x{{ $item->quantity }}
                                            </b>

                                        </span>

                                    @endforeach

                                </div>

                            </td>


                            {{-- Total --}}

                            <td class="p-4">

                                <span class="text-base font-black text-slate-900">

                                    Rp
                                    {{ number_format($grandTotal, 0, ',', '.') }}

                                </span>

                            </td>


                            {{-- Status --}}

                            <td class="p-4 pr-6">

                                @if(
                                    in_array(
                                        $statusStr,
                                        ['menunggu', 'pending']
                                    )
                                )

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-3 py-1.5 text-xs font-extrabold text-amber-700"
                                    >

                                        <i class="fa-solid fa-clock"></i>

                                        Belum Dibayar

                                    </span>

                                @elseif(
                                    in_array(
                                        $statusStr,
                                        [
                                            'diproses',
                                            'process',
                                            'processing',
                                            'cash'
                                        ]
                                    )
                                )

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200/80 bg-blue-50 px-3 py-1.5 text-xs font-extrabold text-blue-700"
                                    >

                                        <i class="fa-solid fa-fire"></i>

                                        Diproses

                                    </span>

                                @elseif(
                                    in_array(
                                        $statusStr,
                                        [
                                            'selesai',
                                            'completed',
                                            'lunas'
                                        ]
                                    )
                                )

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200/80 bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700"
                                    >

                                        <i class="fa-solid fa-check"></i>

                                        Selesai

                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200/80 bg-rose-50 px-3 py-1.5 text-xs font-extrabold text-rose-700"
                                    >

                                        <i class="fa-solid fa-xmark"></i>

                                        Batal

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="p-12 text-center"
                            >

                                <div
                                    class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400"
                                >

                                    <i class="fa-solid fa-inbox"></i>

                                </div>

                                <p class="text-sm font-bold text-slate-700">
                                    Belum ada pesanan masuk
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Setiap pesanan baru dari scan QR pelanggan akan muncul di sini secara langsung.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ================================================================
     SUBSCRIPTION EXPIRED MODAL
================================================================ --}}

@if (($showRenewalModal ?? false) === true)

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @php

        /*
        |--------------------------------------------------------------------------
        | Apakah Ada Pilihan Subscription yang Masih Tersimpan?
        |--------------------------------------------------------------------------
        */

        $hasRenewalSelection =
            !empty($renewalPackage)
            &&
            !empty($renewalDuration)
            &&
            session('subscription.from_public') === true;


        /*
        |--------------------------------------------------------------------------
        | URL
        |--------------------------------------------------------------------------
        */

        $subscriptionIndexUrl =
            route('public.subscription.index');


        $continuePaymentUrl =
            route('public.subscription.account.continue');


        /*
        |--------------------------------------------------------------------------
        | Data Paket
        |--------------------------------------------------------------------------
        */

        $renewalPackageName =
            $renewalPackage->name
            ?? '';


        $renewalDurationName =
            $renewalDuration->name
            ?? '';


        $renewalDurationDays =
            $renewalDuration->duration_days
            ?? 0;


        $renewalPrice =
            $renewalDuration
                ? number_format(
                    $renewalDuration->discount_price
                        ?? $renewalDuration->price,
                    0,
                    ',',
                    '.'
                )
                : '0';

    @endphp


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                |--------------------------------------------------------------------------
                | Data dari Laravel
                |--------------------------------------------------------------------------
                */

                const hasRenewalSelection =
                    @json($hasRenewalSelection);


                const subscriptionIndexUrl =
                    @json($subscriptionIndexUrl);


                const continuePaymentUrl =
                    @json($continuePaymentUrl);


                const packageName =
                    @json($renewalPackageName);


                const durationName =
                    @json($renewalDurationName);


                const durationDays =
                    @json($renewalDurationDays);


                const price =
                    @json($renewalPrice);


                /*
                |--------------------------------------------------------------------------
                | PUBLIC SUBSCRIPTION
                |--------------------------------------------------------------------------
                |
                | Kalau user datang dari public subscription,
                | seharusnya flow tidak berhenti di dashboard.
                |
                | Session package + duration akan diteruskan
                | ke continuePayment.
                |
                */

                if (hasRenewalSelection) {

                    Swal.fire({

                        icon: 'warning',

                        title:
                            'Langganan Anda Telah Berakhir',

                        html:

                            '<div style="text-align:left;">' +

                                '<p style="' +
                                    'font-size:14px;' +
                                    'color:#64748b;' +
                                    'line-height:1.6;' +
                                    'margin-bottom:16px;' +
                                '">' +

                                    'Langganan Anda telah berakhir. ' +
                                    'Pilihan paket dan durasi yang sebelumnya dipilih masih tersimpan.' +

                                '</p>' +


                                '<div style="' +
                                    'background:#f8fafc;' +
                                    'border:1px solid #e2e8f0;' +
                                    'border-radius:14px;' +
                                    'padding:16px;' +
                                '">' +

                                    '<p style="' +
                                        'font-size:11px;' +
                                        'font-weight:800;' +
                                        'text-transform:uppercase;' +
                                        'color:#94a3b8;' +
                                        'margin:0;' +
                                    '">' +

                                        'Paket Dipilih' +

                                    '</p>' +

                                    '<p style="' +
                                        'font-size:18px;' +
                                        'font-weight:900;' +
                                        'color:#0f172a;' +
                                        'margin:5px 0 0;' +
                                    '">' +

                                        packageName +

                                    '</p>' +

                                    '<p style="' +
                                        'font-size:13px;' +
                                        'font-weight:600;' +
                                        'color:#64748b;' +
                                        'margin:5px 0 0;' +
                                    '">' +

                                        durationName +
                                        ' · ' +
                                        durationDays +
                                        ' hari' +

                                    '</p>' +

                                    '<p style="' +
                                        'font-size:20px;' +
                                        'font-weight:900;' +
                                        'color:#f59e0b;' +
                                        'margin:10px 0 0;' +
                                    '">' +

                                        'Rp ' +
                                        price +

                                    '</p>' +

                                '</div>' +

                            '</div>',


                        showCancelButton:
                            true,


                        confirmButtonText:
                            'Lanjutkan Pembayaran',


                        cancelButtonText:
                            'Pilih Paket Lain',


                        confirmButtonColor:
                            '#f59e0b',


                        cancelButtonColor:
                            '#0f172a',


                        allowOutsideClick:
                            false,


                        allowEscapeKey:
                            false,


                        reverseButtons:
                            true

                    }).then(
                        function (result) {

                            if (result.isConfirmed) {

                                window.location.replace(
                                    continuePaymentUrl
                                );

                                return;
                            }


                            if (
                                result.dismiss ===
                                Swal.DismissReason.cancel
                            ) {

                                window.location.replace(
                                    subscriptionIndexUrl
                                );
                            }

                        }
                    );


                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | LOGIN BIASA + EXPIRED
                |--------------------------------------------------------------------------
                */

                Swal.fire({

                    icon:
                        'warning',


                    title:
                        'Langganan Anda Telah Berakhir',


                    text:
                        'Masa berlangganan Anda telah berakhir. Silakan pilih paket untuk memperpanjang langganan.',


                    confirmButtonText:
                        'Perpanjang Langganan',


                    confirmButtonColor:
                        '#f59e0b',


                    allowOutsideClick:
                        false,


                    allowEscapeKey:
                        false

                }).then(
                    function (result) {

                        if (result.isConfirmed) {

                            window.location.replace(
                                subscriptionIndexUrl
                            );

                        }

                    }
                );

            }
        );

    </script>

@endif

@endsection
