@extends('layouts.merchant')

@section('header')
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                {{ Auth::user()->role === 'kasir' ? 'Kelola Pesanan Masuk' : 'Riwayat Pesanan' }}
            </h2>

            <p class="text-xs font-medium text-slate-500 mt-1">
                {{ Auth::user()->role === 'kasir'
                    ? 'Validasi pembayaran dan pantau status pesanan pelanggan hari ini secara real-time.'
                    : 'Pantau seluruh riwayat transaksi pelanggan dan rekap pendapatan.' }}
            </p>
        </div>

        {{-- FORM FILTER --}}
        <div x-data="{ filterOpen: true }" class="flex items-center justify-end">

            <form method="GET" action="{{ route('merchant.orders.index') }}"
                class="flex items-center gap-2
               bg-slate-900
               p-2
               rounded-2xl
               shadow-lg
               border border-slate-800
               transition-all duration-300">

                {{-- TOGGLE FILTER --}}
                <button type="button" @click="filterOpen = !filterOpen"
                    class="pl-2 pr-1
                   text-amber-400
                   text-xs
                   font-extrabold
                   flex
                   items-center
                   gap-1.5
                   cursor-pointer
                   shrink-0"
                    title="Tampilkan/Sembunyikan filter">
                    <i class="fa-solid fa-sliders"></i>

                    <span x-show="filterOpen" x-transition class="hidden sm:inline text-slate-300">
                        Filter:
                    </span>
                </button>


                {{-- ISI FILTER --}}
                <div x-show="filterOpen" x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="opacity-0 max-w-0" x-transition:enter-end="opacity-100 max-w-[500px]"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="opacity-100 max-w-[500px]" x-transition:leave-end="opacity-0 max-w-0"
                    class="flex items-center gap-2 overflow-hidden">

                    {{-- JENIS FILTER --}}
                    <select name="filter_type" id="filterTypeSelect" onchange="switchFilterMode(this.value)"
                        class="bg-slate-800
                       text-amber-400
                       text-xs
                       font-bold
                       rounded-xl
                       px-3 py-2
                       border border-slate-700/80
                       focus:outline-none
                       focus:ring-2
                       focus:ring-amber-500
                       cursor-pointer
                       shrink-0">

                        <option value="day" {{ ($filterType ?? 'day') === 'day' ? 'selected' : '' }}>
                            📅 Per Hari
                        </option>

                        <option value="month" {{ ($filterType ?? '') === 'month' ? 'selected' : '' }}>
                            🗓️ Per Bulan
                        </option>

                        <option value="year" {{ ($filterType ?? '') === 'year' ? 'selected' : '' }}>
                            📊 Per Tahun
                        </option>

                    </select>


                    {{-- FILTER HARI --}}
                    <div id="inputDayWrapper" class="{{ ($filterType ?? 'day') === 'day' ? 'block' : 'hidden' }}">
                        <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}"
                            class="bg-slate-800
                           text-white
                           text-xs
                           font-bold
                           rounded-xl
                           px-3 py-2
                           border border-slate-700/80
                           shrink-0">
                    </div>


                    {{-- FILTER BULAN --}}
                    <div id="inputMonthWrapper" class="{{ ($filterType ?? '') === 'month' ? 'block' : 'hidden' }}">
                        <input type="month" name="month" value="{{ $selectedMonth ?? date('Y-m') }}"
                            class="bg-slate-800
                           text-white
                           text-xs
                           font-bold
                           rounded-xl
                           px-3 py-2
                           border border-slate-700/80
                           shrink-0">
                    </div>


                    {{-- FILTER TAHUN --}}
                    <div id="inputYearWrapper" class="{{ ($filterType ?? '') === 'year' ? 'block' : 'hidden' }}">
                        <select name="year"
                            class="bg-slate-800
                           text-white
                           text-xs
                           font-bold
                           rounded-xl
                           px-3 py-2
                           border border-slate-700/80
                           shrink-0">

                            @for ($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}"
                                    {{ ($selectedYear ?? date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor

                        </select>
                    </div>


                    {{-- TERAPKAN --}}
                    <button type="submit"
                        class="bg-amber-500
                       hover:bg-amber-400
                       text-slate-950
                       px-4 py-2
                       rounded-xl
                       text-xs
                       font-black
                       transition
                       cursor-pointer
                       shrink-0">
                        Terapkan
                    </button>

                </div>

            </form>

        </div>
    </div>
@endsection


@section('content')

    <div class="space-y-6">

        <!-- TABEL PESANAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

            <div class="p-6 border-b border-slate-100 flex items-center justify-between">

                <div>

                    <h3 class="font-extrabold text-slate-900 text-lg">
                        {{ Auth::user()->role === 'kasir' ? 'Antrean Pesanan Masuk' : 'Daftar Transaksi Pelanggan' }}
                    </h3>

                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ Auth::user()->role === 'kasir'
                            ? 'Daftar pesanan hari ini dari meja pelanggan'
                            : 'Semua riwayat pesanan periode ' . ($labelPeriode ?? 'Hari Ini') }}
                    </p>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm text-left">

                    <thead
                        class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">

                        <tr>

                            <th class="p-4 pl-6">
                                No. Order
                            </th>

                            <th class="p-4">
                                @if (Auth::user()->role !== 'dapur')
                                    Pelanggan
                                @endif
                            </th>

                            <th class="p-4">
                                Pesanan
                            </th>

                            <th class="p-4">
                                @if (Auth::user()->role !== 'dapur')
                                    Metode Bayar
                                @endif
                            </th>

                            @if (Auth::user()->role !== 'dapur')
                                <th class="p-4">
                                    Total
                                </th>

                                <th class="p-4 text-center">

                                    @if (Auth::user()->role === 'kasir')
                                        Status Pesanan
                                    @else
                                        Diselesaikan
                                    @endif

                                </th>
                            @endif

                            <th class="p-4 pr-6 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($orders as $order)
                            @php

                                $itemTotal = $order->items->sum(function ($item) {
                                    return $item->subtotal ?? $item->price * $item->quantity;
                                });

                                /*
                                |--------------------------------------------------------------------------
                                | Total order menggunakan kolom total dari database
                                |--------------------------------------------------------------------------
                                */
                                $grandTotal = (float) ($order->total ?? $itemTotal);

                                /*
                                |--------------------------------------------------------------------------
                                | Deteksi pembayaran cash
                                |--------------------------------------------------------------------------
                                */
                                $isCash = in_array(strtolower($order->payment_method ?? ''), [
                                    'cash',
                                    'kasir',
                                    'tunai',
                                ]);

                                /*
                                |--------------------------------------------------------------------------
                                | Status makanan
                                |--------------------------------------------------------------------------
                                */
                                $statusStr = strtolower($order->status ?? '');

                                /*
                                |--------------------------------------------------------------------------
                                | Status pembayaran
                                |--------------------------------------------------------------------------
                                */
                                $paymentStatus = strtolower($order->payment_status ?? 'pending');

                            @endphp

                            {{-- STATUS UNIT --}}

                            @php
                                $unitStatusData = [];

                                foreach ($order->items as $item) {
                                    foreach ($item->unit ?? collect() as $unit) {
                                        $unitStatusData[] = [
                                            'menu' => $item->menu_name ?? ($item->menu->name ?? 'Menu'),

                                            'unit' => $unit->unit_number,

                                            'status' => $unit->status,
                                        ];
                                    }
                                }
                            @endphp


                            <tr class="hover:bg-slate-50/60 transition" data-order-id="{{ $order->id }}"
                                data-order-units='@json($unitStatusData)'>


                                {{-- ==========================================================
                                    NO. ORDER & MEJA
                                =========================================================== --}}
                                <td class="p-4 pl-6">

                                    <span class="font-black text-slate-900 block text-base">

                                        @if (Auth::user()->role === 'dapur')
                                            #{{ substr($order->order_number, -6) }}
                                        @else
                                            #{{ $order->order_number }}
                                        @endif

                                    </span>


                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-md mt-1 border border-amber-200/60">

                                        <i class="fa-solid fa-location-dot"></i>

                                        {{ $order->qrCode->name ?? 'Meja General' }}

                                    </span>

                                </td>


                                {{-- ==========================================================
                                    PELANGGAN
                                =========================================================== --}}
                                <td class="p-4">

                                    @if (Auth::user()->role !== 'dapur')
                                        <p class="font-extrabold text-slate-800">
                                            {{ $order->customer_name }}
                                        </p>
                                    @endif

                                </td>


                                {{-- ==========================================================
                                    PESANAN / MENU
                                =========================================================== --}}
                                <td class="p-4">

                                    <div class="space-y-3">

                                        @foreach ($order->items as $item)
                                            @for ($i = 0; $i < $item->quantity; $i++)
                                                <div class="h-[40px] flex items-center gap-2 text-xs">

                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>

                                                    <span class="font-semibold text-slate-700">
                                                        {{ $item->menu_name ?? ($item->menu->name ?? 'Menu') }}
                                                    </span>

                                                    <span
                                                        class="font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[10px]">
                                                        (x1)
                                                    </span>

                                                </div>
                                            @endfor
                                        @endforeach

                                    </div>

                                </td>


                                {{-- ==========================================================
                                    METODE BAYAR
                                =========================================================== --}}
                                <td class="p-4">

                                    @if (Auth::user()->role !== 'dapur')
                                        <div class="flex flex-col items-start gap-1.5">

                                            {{-- Metode pembayaran --}}
                                            @if ($isCash)
                                                <span
                                                    class="px-3 py-1 text-xs bg-slate-100 text-slate-800 font-extrabold rounded-xl border border-slate-200/80 inline-flex items-center gap-1.5">

                                                    <i class="fa-solid fa-cash-register text-slate-500"></i>

                                                    Bayar Kasir

                                                </span>
                                            @else
                                                <span
                                                    class="px-3 py-1 text-xs bg-amber-50 text-amber-700 font-extrabold rounded-xl border border-amber-200/80 inline-flex items-center gap-1.5">

                                                    <i class="fa-solid fa-qrcode text-amber-600"></i>

                                                    QRIS

                                                </span>
                                            @endif


                                            {{-- ==================================================
                                                STATUS PEMBAYARAN KHUSUS KASIR
                                            =================================================== --}}
                                            @if (Auth::user()->role === 'kasir')
                                                @if ($paymentStatus === 'paid')
                                                    <span
                                                        class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-black rounded-lg border border-emerald-200/80 inline-flex items-center gap-1">

                                                        <i class="fa-solid fa-circle-check text-[10px]"></i>

                                                        Dibayar

                                                    </span>
                                                @elseif ($isCash && $paymentStatus === 'pending')
                                                    <span
                                                        class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[11px] font-black rounded-lg border border-amber-200/80 inline-flex items-center gap-1">

                                                        <i class="fa-regular fa-clock text-[10px]"></i>

                                                        Belum Dibayar

                                                    </span>
                                                @elseif ($paymentStatus === 'failed')
                                                    <span
                                                        class="px-2.5 py-1 bg-rose-50 text-rose-700 text-[11px] font-black rounded-lg border border-rose-200/80 inline-flex items-center gap-1">

                                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>

                                                        Gagal

                                                    </span>
                                                @elseif ($paymentStatus === 'expired')
                                                    <span
                                                        class="px-2.5 py-1 bg-rose-50 text-rose-700 text-[11px] font-black rounded-lg border border-rose-200/80 inline-flex items-center gap-1">

                                                        <i class="fa-solid fa-clock text-[10px]"></i>

                                                        Kedaluwarsa

                                                    </span>
                                                @endif
                                            @endif

                                        </div>
                                    @endif

                                </td>


                                {{-- ==========================================================
                                    TOTAL HARGA
                                =========================================================== --}}
                                @if (Auth::user()->role !== 'dapur')
                                    <td class="p-4">

                                        <span class="font-black text-slate-900 text-base">

                                            Rp {{ number_format($grandTotal, 0, ',', '.') }}

                                        </span>

                                    </td>
                                @endif


                                {{-- ==========================================================
                                    STATUS PESANAN / MAKANAN
                                =========================================================== --}}
                                @if (Auth::user()->role !== 'dapur')
                                    <td class="p-4 text-center">

                                        <div class="flex flex-col items-center justify-center gap-1">

                                            {{-- ==========================================================
                                                SEMUA MENU SELESAI
                                            =========================================================== --}}
                                            @if ($order->display_status === 'completed')
                                                <span
                                                    class="px-2.5 py-1
                                                        bg-emerald-50
                                                        text-emerald-700
                                                        text-xs
                                                        font-black
                                                        rounded-lg
                                                        border border-emerald-200/80
                                                        inline-flex
                                                        items-center
                                                        gap-1">
                                                    <i class="fa-solid fa-check text-[10px]"></i>
                                                    Selesai
                                                </span>


                                                <span class="text-[11px] font-bold text-emerald-600">
                                                    {{ $order->status_summary['completed'] }}
                                                    menu selesai
                                                </span>


                                                {{-- ==========================================================
                                                    SEMUA MENU BAHAN HABIS
                                                =========================================================== --}}
                                            @elseif ($order->display_status === 'cancelled')
                                                <span
                                                    class="px-2.5 py-1
                                                        bg-rose-50
                                                        text-rose-700
                                                        text-xs
                                                        font-black
                                                        rounded-lg
                                                        border border-rose-200/80
                                                        inline-flex
                                                        items-center
                                                        gap-1">
                                                    <i class="fa-solid fa-box-open text-[10px]"></i>
                                                    Bahan Habis
                                                </span>


                                                <span class="text-[11px] font-bold text-rose-600">
                                                    {{ $order->status_summary['cancelled'] }}
                                                    menu bahan habis
                                                </span>


                                                {{-- ==========================================================
                                                    SEBAGIAN MENU BERMASALAH
                                                =========================================================== --}}
                                            @elseif ($order->display_status === 'partial_problem')
                                                <span
                                                    class="px-2.5 py-1
                                                        bg-orange-50
                                                        text-orange-700
                                                        text-xs
                                                        font-black
                                                        rounded-lg
                                                        border border-orange-200/80
                                                        inline-flex
                                                        items-center
                                                        gap-1">
                                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                    Sebagian Bermasalah
                                                </span>


                                                <span class="text-[11px] font-bold text-slate-500">

                                                    {{ $order->status_summary['completed'] }}
                                                    selesai

                                                    <span class="text-slate-300">•</span>

                                                    {{ $order->status_summary['cancelled'] }}
                                                    bahan habis

                                                </span>


                                                {{-- ==========================================================
                                                    MASIH DIPROSES
                                                =========================================================== --}}
                                            @elseif ($order->display_status === 'processing')
                                                <span
                                                    class="px-2.5 py-1
                                                        bg-blue-50
                                                        text-blue-700
                                                        text-xs
                                                        font-black
                                                        rounded-lg
                                                        border border-blue-200/80
                                                        inline-flex
                                                        items-center
                                                        gap-1">
                                                    <i class="fa-solid fa-fire text-[10px]"></i>
                                                    Diproses
                                                </span>


                                                <span class="text-[11px] font-bold text-blue-600">

                                                    {{ $order->status_summary['processing'] }}
                                                    sedang diproses

                                                </span>


                                                {{-- ==========================================================
                                                    MENUNGGU
                                                =========================================================== --}}
                                            @else
                                                <span
                                                    class="px-2.5 py-1
                                                        bg-amber-50
                                                        text-amber-700
                                                        text-xs
                                                        font-black
                                                        rounded-lg
                                                        border border-amber-200/80
                                                        inline-flex
                                                        items-center
                                                        gap-1">
                                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                                    Menunggu
                                                </span>


                                                <span class="text-[11px] font-bold text-amber-600">

                                                    {{ $order->status_summary['pending'] }}
                                                    menunggu diproses

                                                </span>
                                            @endif

                                            @if ($order->display_status === 'partial_problem')
                                                <button type="button"
                                                    onclick="openOrderStatusModal('{{ $order->order_number }}', {{ $order->id }})"
                                                    class="mt-1 px-2.5 py-1
               bg-slate-100
               hover:bg-slate-200
               text-slate-600
               rounded-lg
               text-[10px]
               font-extrabold
               transition
               cursor-pointer">
                                                    <i class="fa-solid fa-eye mr-1"></i>
                                                    Lihat Detail
                                                </button>
                                            @endif


                                            {{-- WAKTU ORDER --}}
                                            <span class="text-[11px] font-bold text-slate-500">
                                                {{ $order->created_at->format('d/m/Y H:i') }} WIB
                                            </span>

                                        </div>

                                    </td>
                                @endif


                                {{-- ==========================================================
                                    AKSI
                                =========================================================== --}}
                                <td class="p-4 pr-6 text-center">


                                    {{-- ======================================================
    KHUSUS DAPUR
    STATUS PER UNIT MENU
======================================================= --}}
                                    @if (Auth::user()->role === 'dapur')
                                        <div class="space-y-3">

                                            @foreach ($order->items as $item)
                                                @php
                                                    $units = \App\Models\OrderItemUnit::where(
                                                        'order_item_id',
                                                        $item->id,
                                                    )
                                                        ->orderBy('unit_number')
                                                        ->get();
                                                @endphp

                                                @foreach ($units as $unit)
                                                    <div class="h-[40px] flex items-center justify-center gap-2">


                                                        {{-- ==================================================
                                                            PENDING / PROCESSING
                                                        ================================================== --}}
                                                        @if (in_array($unit->status, ['pending', 'processing']))
                                                            {{-- ==============================================
                                                                SELESAI
                                                            =============================================== --}}
                                                            <form
                                                                action="{{ route('merchant.orders.unit.status', encryptId($unit->id)) }}"
                                                                method="POST" class="inline-block">

                                                                @csrf
                                                                @method('PATCH')

                                                                <input type="hidden" name="status" value="completed">

                                                                <button type="submit"
                                                                    class="px-3 py-2
                                       bg-emerald-600
                                       hover:bg-emerald-500
                                       text-white
                                       rounded-xl
                                       text-xs
                                       font-black
                                       transition
                                       cursor-pointer
                                       whitespace-nowrap">
                                                                    <i class="fa-solid fa-check mr-1"></i>
                                                                    Selesai
                                                                </button>

                                                            </form>


                                                            {{-- ==============================================
                            BAHAN HABIS
                        =============================================== --}}
                                                            <form
                                                                action="{{ route('merchant.orders.unit.status', encryptId($unit->id)) }}"
                                                                method="POST" class="cancel-food-form inline-block">

                                                                @csrf
                                                                @method('PATCH')

                                                                <input type="hidden" name="status" value="cancelled">

                                                                <button type="submit"
                                                                    class="cancel-food-btn
                                       px-3 py-2
                                       bg-rose-100
                                       hover:bg-rose-200
                                       text-rose-700
                                       rounded-xl
                                       text-xs
                                       font-black
                                       border
                                       border-rose-200
                                       transition
                                       cursor-pointer
                                       whitespace-nowrap">
                                                                    <i class="fa-solid fa-box-open mr-1"></i>
                                                                    Bahan Habis
                                                                </button>

                                                            </form>


                                                            {{-- ==================================================
                        SUDAH SELESAI
                    ================================================== --}}
                                                        @elseif ($unit->status === 'completed')
                                                            <span
                                                                class="px-3 py-2
                                   bg-emerald-50
                                   text-emerald-700
                                   rounded-xl
                                   text-xs
                                   font-black
                                   border
                                   border-emerald-200
                                   whitespace-nowrap">
                                                                <i class="fa-solid fa-circle-check mr-1"></i>
                                                                Selesai
                                                            </span>


                                                            {{-- ==================================================
                        BAHAN HABIS
                    ================================================== --}}
                                                        @elseif ($unit->status === 'cancelled')
                                                            <span
                                                                class="px-3 py-2
                                   bg-rose-50
                                   text-rose-700
                                   rounded-xl
                                   text-xs
                                   font-black
                                   border
                                   border-rose-200
                                   whitespace-nowrap">
                                                                <i class="fa-solid fa-box-open mr-1"></i>
                                                                Bahan Habis
                                                            </span>
                                                        @endif

                                                    </div>
                                                @endforeach
                                            @endforeach

                                        </div>


                                        {{-- ======================================================
                                        KHUSUS KASIR
                                    ======================================================= --}}
                                    @elseif(Auth::user()->role === 'kasir')
                                        <div class="flex items-center justify-center gap-2">

                                            {{-- CETAK STRUK --}}
                                            <a href="{{ route('merchant.orders.receipt', encryptId($order->id)) }}"
                                                target="_blank"
                                                class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl flex items-center justify-center text-xs transition"
                                                title="Cetak Struk">

                                                <i class="fa-solid fa-print"></i>

                                            </a>


                                            {{-- ==================================================
                                                KONFIRMASI PEMBAYARAN CASH
                                            =================================================== --}}
                                            @if ($isCash && $paymentStatus === 'pending')
                                                <button type="button"
                                                    onclick="openCashPaymentModal(
                                                        '{{ encryptId($order->id) }}',
                                                        {{ (float) $grandTotal }},
                                                        '{{ $order->order_number }}'
                                                    )"
                                                    class="px-3 py-2
                                                        rounded-xl
                                                        bg-emerald-100
                                                        hover:bg-emerald-200
                                                        border border-emerald-200
                                                        text-emerald-700
                                                        text-xs
                                                        font-extrabold
                                                        transition">

                                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                                    Sudah Dibayar

                                                </button>
                                            @endif


                                            {{-- ==================================================
                                                BATALKAN PESANAN
                                                Hanya sebelum pembayaran
                                            =================================================== --}}
                                            @if ($paymentStatus === 'pending' && !in_array($statusStr, ['completed', 'cancelled']))
                                                <form
                                                    action="{{ route('merchant.orders.status', encryptId($order->id)) }}"
                                                    method="POST" class="cancel-food-form inline-block">

                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="status" value="cancelled">

                                                    <button type="submit"
                                                        class="cancel-food-btn px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl text-xs font-extrabold transition cursor-pointer border border-rose-200"
                                                        title="Batalkan">

                                                        <i class="fa-solid fa-xmark"></i>

                                                    </button>

                                                </form>
                                            @endif

                                        </div>


                                        {{-- ======================================================
                                        OWNER
                                        LOGIKA TIDAK DIUBAH
                                    ======================================================= --}}
                                    @else
                                        <!-- HAMBURGER MENU / DROPDOWN UNTUK OWNER -->

                                        <div class="relative inline-block text-left" x-data="{ open: false }">

                                            <button @click="open = !open" @click.outside="open = false" type="button"
                                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition focus:outline-none cursor-pointer">

                                                <i class="fa-solid fa-ellipsis-vertical"></i>

                                            </button>


                                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute right-0 z-50 mt-2 w-40 origin-top-right rounded-2xl bg-white shadow-xl border border-slate-100 py-1.5 focus:outline-none">


                                                <a href="{{ route('merchant.orders.receipt', encryptId($order->id)) }}"
                                                    target="_blank"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">

                                                    <i class="fa-solid fa-print text-amber-500 w-4"></i>

                                                    Cetak Struk

                                                </a>


                                                <a href="#"
                                                    class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">

                                                    <i class="fa-solid fa-pen-to-square text-blue-500 w-4"></i>

                                                    Edit Pesanan

                                                </a>


                                                <div class="my-1 border-t border-slate-100"></div>


                                                <form action="#" method="POST">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="button" onclick="alert('Fitur hapus belum diaktifkan')"
                                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition text-left cursor-pointer">

                                                        <i class="fa-solid fa-trash-can w-4"></i>

                                                        Hapus

                                                    </button>

                                                </form>

                                            </div>

                                        </div>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="{{ Auth::user()->role === 'dapur' ? 5 : 7 }}" class="p-12 text-center">

                                    <div
                                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">

                                        <i class="fa-solid fa-inbox"></i>

                                    </div>

                                    <p class="font-bold text-slate-700 text-sm">
                                        Belum ada pesanan masuk
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Pesanan pelanggan akan otomatis muncul di sini.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- =========================================================
    MODAL PEMBAYARAN KASIR
========================================================= --}}

    <div id="cashPaymentModal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center
           bg-slate-950/50 backdrop-blur-sm px-4">

        <div
            class="w-full max-w-md
               overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white
               shadow-2xl">

            {{-- HEADER --}}
            <div
                class="flex items-center justify-between
                   border-b border-slate-100
                   px-6 py-4">

                <div>

                    <h3 class="text-lg font-black text-slate-900">

                        Pembayaran Kasir

                    </h3>

                    <p id="cashPaymentOrder" class="mt-1 text-xs font-medium text-slate-400">

                        -

                    </p>

                </div>

                <button type="button" onclick="closeCashPaymentModal()"
                    class="flex h-9 w-9 items-center justify-center
                       rounded-xl
                       bg-slate-100
                       text-slate-500
                       transition
                       hover:bg-slate-200">

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>


            {{-- BODY --}}
            <div class="space-y-5 p-6">

                {{-- 1. TOTAL TAGIHAN --}}
                <div
                    class="rounded-2xl
                       border border-slate-200
                       bg-slate-50
                       p-4">

                    <p
                        class="text-[11px]
                           font-extrabold
                           uppercase
                           tracking-wider
                           text-slate-400">

                        Total Tagihan

                    </p>

                    <div id="cashPaymentTotal"
                        class="mt-1
                           text-2xl
                           font-black
                           text-slate-900">

                        Rp 0

                    </div>

                </div>


                {{-- 2. NOMINAL UANG PELANGGAN --}}
                <div>

                    <label for="cashReceivedInput"
                        class="mb-2 block
                           text-xs
                           font-extrabold
                           text-slate-700">

                        Nominal Uang Pelanggan

                    </label>

                    <div class="relative">

                        <span
                            class="absolute
                               left-4
                               top-1/2
                               -translate-y-1/2
                               text-sm
                               font-black
                               text-slate-400">

                            Rp

                        </span>

                        <input type="number" id="cashReceivedInput" min="0" step="100"
                            placeholder="Masukkan uang pelanggan"
                            class="w-full
                               rounded-xl
                               border border-slate-200
                               bg-white
                               py-3
                               pl-11
                               pr-4
                               text-sm
                               font-bold
                               text-slate-900
                               outline-none
                               transition
                               focus:border-emerald-500
                               focus:ring-4
                               focus:ring-emerald-500/10">

                    </div>

                    <p id="cashPaymentError"
                        class="mt-2 hidden
                           text-xs
                           font-bold
                           text-red-600">

                    </p>

                </div>


                {{-- 3. KEMBALIAN --}}
                <div
                    class="rounded-2xl
                       border border-emerald-100
                       bg-emerald-50
                       p-4">

                    <div class="flex items-center justify-between">

                        <span
                            class="text-xs
                               font-extrabold
                               text-emerald-700">

                            Kembalian

                        </span>

                        <span id="cashPaymentChange"
                            class="text-xl
                               font-black
                               text-emerald-700">

                            Rp 0

                        </span>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div
                class="flex items-center justify-end
                   gap-2
                   border-t border-slate-100
                   bg-slate-50/50
                   px-6 py-4">

                <button type="button" onclick="closeCashPaymentModal()"
                    class="rounded-xl
                       bg-slate-100
                       px-4 py-2.5
                       text-xs
                       font-extrabold
                       text-slate-600
                       transition
                       hover:bg-slate-200">

                    Batal

                </button>


                {{-- 4. KONFIRMASI --}}
                <form id="cashPaymentForm" method="POST">

                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="cash_received" id="cashReceivedHidden">

                    <button type="submit" id="cashPaymentSubmit" disabled
                        class="rounded-xl
                           bg-emerald-600
                           px-4 py-2.5
                           text-xs
                           font-black
                           text-white
                           transition
                           hover:bg-emerald-500
                           disabled:cursor-not-allowed
                           disabled:bg-slate-200
                           disabled:text-slate-400">

                        <i class="fa-solid fa-check mr-1"></i>

                        Konfirmasi Pembayaran

                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
    MODAL DETAIL STATUS PESANAN
========================================================= --}}

    <div id="orderStatusModal"
        class="fixed inset-0 z-[9998] hidden items-center justify-center
           bg-slate-950/50 backdrop-blur-sm px-4">

        <div
            class="w-full max-w-md
               overflow-hidden
               rounded-2xl
               border border-slate-200
               bg-white
               shadow-2xl">

            {{-- HEADER --}}
            <div
                class="flex items-center justify-between
                   border-b border-slate-100
                   px-6 py-4">

                <div>

                    <h3 class="text-lg font-black text-slate-900">
                        Detail Status Pesanan
                    </h3>

                    <p id="orderStatusOrderNumber" class="mt-1 text-xs font-medium text-slate-400">
                        -
                    </p>

                </div>


                <button type="button" onclick="closeOrderStatusModal()"
                    class="flex h-9 w-9 items-center justify-center
                       rounded-xl
                       bg-slate-100
                       text-slate-500
                       transition
                       hover:bg-slate-200">

                    <i class="fa-solid fa-xmark"></i>

                </button>

            </div>


            {{-- BODY --}}
            <div class="p-6">

                <div id="orderStatusUnitList" class="space-y-2">
                </div>

            </div>


            {{-- FOOTER --}}
            <div id="orderStatusSummary"
                class="border-t border-slate-100
                   bg-slate-50/50
                   px-6 py-4
                   text-center
                   text-xs
                   font-extrabold
                   text-slate-600">
                -
            </div>

        </div>

    </div>


    {{-- SCRIPT UNTUK MODAL PEMBAYARAN KASIR --}}


    <script>
        let cashPaymentTotal = 0;


        /*
        |--------------------------------------------------------------------------
        | FORMAT RUPIAH
        |--------------------------------------------------------------------------
        */

        function formatRupiah(value) {

            return 'Rp ' + Number(value).toLocaleString(
                'id-ID'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | BUKA MODAL
        |--------------------------------------------------------------------------
        */

        function openCashPaymentModal(
            encryptedId,
            total,
            orderNumber
        ) {

            cashPaymentTotal = Number(total);


            const modal =
                document.getElementById(
                    'cashPaymentModal'
                );

            const form =
                document.getElementById(
                    'cashPaymentForm'
                );

            const input =
                document.getElementById(
                    'cashReceivedInput'
                );


            /*
            |----------------------------------------------------------------------
            | SET ACTION FORM
            |----------------------------------------------------------------------
            */

            form.action =
                "{{ url('/merchant/orders') }}/" +
                encryptedId +
                "/payment";


            /*
            |----------------------------------------------------------------------
            | TAMPILKAN DATA
            |----------------------------------------------------------------------
            */

            document.getElementById(
                    'cashPaymentOrder'
                ).textContent =
                '#' + orderNumber;


            document.getElementById(
                    'cashPaymentTotal'
                ).textContent =
                formatRupiah(
                    cashPaymentTotal
                );


            /*
            |----------------------------------------------------------------------
            | RESET
            |----------------------------------------------------------------------
            */

            input.value = '';

            document.getElementById(
                'cashReceivedHidden'
            ).value = '';


            document.getElementById(
                    'cashPaymentChange'
                ).textContent =
                'Rp 0';


            document.getElementById(
                'cashPaymentError'
            ).textContent = '';


            document.getElementById(
                'cashPaymentError'
            ).classList.add(
                'hidden'
            );


            document.getElementById(
                'cashPaymentSubmit'
            ).disabled = true;


            /*
            |----------------------------------------------------------------------
            | TAMPILKAN MODAL
            |----------------------------------------------------------------------
            */

            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );


            setTimeout(() => {

                input.focus();

            }, 100);

        }


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        function closeCashPaymentModal() {

            const modal =
                document.getElementById(
                    'cashPaymentModal'
                );


            modal.classList.add(
                'hidden'
            );

            modal.classList.remove(
                'flex'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | HITUNG KEMBALIAN
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'cashReceivedInput'
            )
            .addEventListener(
                'input',
                function() {

                    const received =
                        Number(
                            this.value
                        ) || 0;


                    const change =
                        received -
                        cashPaymentTotal;


                    const changeElement =
                        document.getElementById(
                            'cashPaymentChange'
                        );

                    const errorElement =
                        document.getElementById(
                            'cashPaymentError'
                        );

                    const submitButton =
                        document.getElementById(
                            'cashPaymentSubmit'
                        );

                    const hiddenInput =
                        document.getElementById(
                            'cashReceivedHidden'
                        );


                    /*
                    |------------------------------------------------------------------
                    | SIMPAN NILAI
                    |------------------------------------------------------------------
                    */

                    hiddenInput.value =
                        received;


                    /*
                    |------------------------------------------------------------------
                    | BELUM DIISI
                    |------------------------------------------------------------------
                    */

                    if (
                        received <= 0
                    ) {

                        changeElement.textContent =
                            'Rp 0';

                        errorElement.textContent =
                            '';

                        errorElement.classList.add(
                            'hidden'
                        );

                        submitButton.disabled =
                            true;

                        return;

                    }


                    /*
                    |------------------------------------------------------------------
                    | UANG KURANG
                    |------------------------------------------------------------------
                    */

                    if (
                        received < cashPaymentTotal
                    ) {

                        changeElement.textContent =
                            'Rp 0';


                        errorElement.textContent =
                            'Uang pelanggan kurang ' +
                            formatRupiah(
                                cashPaymentTotal -
                                received
                            );


                        errorElement.classList.remove(
                            'hidden'
                        );


                        submitButton.disabled =
                            true;

                        return;

                    }


                    /*
                    |------------------------------------------------------------------
                    | UANG CUKUP / LEBIH
                    |------------------------------------------------------------------
                    */

                    errorElement.classList.add(
                        'hidden'
                    );


                    changeElement.textContent =
                        formatRupiah(
                            change
                        );


                    submitButton.disabled =
                        false;

                }
            );


        /*
        |--------------------------------------------------------------------------
        | KLIK DI LUAR MODAL
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'cashPaymentModal'
            )
            .addEventListener(
                'click',
                function(event) {

                    if (
                        event.target === this
                    ) {

                        closeCashPaymentModal();

                    }

                }
            );
    </script>

    {{-- ==============================================================
        FILTER SCRIPT
    ============================================================== --}}
    <script>
        function switchFilterMode(mode) {

            document.getElementById('inputDayWrapper')?.classList.add('hidden');
            document.getElementById('inputMonthWrapper')?.classList.add('hidden');
            document.getElementById('inputYearWrapper')?.classList.add('hidden');

            if (mode === 'day') {
                document.getElementById('inputDayWrapper')?.classList.remove('hidden');
            } else if (mode === 'month') {
                document.getElementById('inputMonthWrapper')?.classList.remove('hidden');
            } else if (mode === 'year') {
                document.getElementById('inputYearWrapper')?.classList.remove('hidden');
            }
        }
    </script>


    {{-- ==============================================================
        CANCEL CONFIRMATION
    ============================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.cancel-food-form').forEach(function(form) {

                form.addEventListener('submit', function(e) {

                    e.preventDefault();

                    Swal.fire({

                        icon: 'warning',

                        title: 'Cancel Makanan?',

                        text: 'Pesanan ini akan dibatalkan.',

                        showCancelButton: true,

                        confirmButtonText: 'Ya, Cancel',

                        cancelButtonText: 'Batal',

                        confirmButtonColor: '#e11d48',

                        cancelButtonColor: '#64748b',

                        background: '#ffffff',

                        color: '#111827',

                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-5 py-2.5 font-bold',
                            cancelButton: 'rounded-xl px-5 py-2.5 font-bold'
                        }

                    }).then(function(result) {

                        if (result.isConfirmed) {
                            form.submit();
                        }

                    });

                });

            });

        });
    </script>


    {{-- SCRIPT MODAL DETAIL --}}
    <script>
        /*
    |--------------------------------------------------------------------------
    | BUKA MODAL DETAIL STATUS PESANAN
    |--------------------------------------------------------------------------
    */

        function openOrderStatusModal(orderNumber, orderId) {

            /*
            |--------------------------------------------------------------------------
            | CARI BARIS ORDER
            |--------------------------------------------------------------------------
            */

            const row = document.querySelector(
                `tr[data-order-id="${orderId}"]`
            );


            if (!row) {

                console.error(
                    'Baris order tidak ditemukan:',
                    orderId
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA UNIT
            |--------------------------------------------------------------------------
            */

            let units = [];

            try {

                units = JSON.parse(
                    row.dataset.orderUnits || '[]'
                );

            } catch (error) {

                console.error(
                    'Gagal membaca data unit:',
                    error
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | ELEMENT MODAL
            |--------------------------------------------------------------------------
            */

            const modal =
                document.getElementById(
                    'orderStatusModal'
                );

            const orderNumberElement =
                document.getElementById(
                    'orderStatusOrderNumber'
                );

            const list =
                document.getElementById(
                    'orderStatusUnitList'
                );

            const summary =
                document.getElementById(
                    'orderStatusSummary'
                );


            if (
                !modal ||
                !orderNumberElement ||
                !list ||
                !summary
            ) {

                console.error(
                    'Element modal status tidak ditemukan.'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | NOMOR ORDER
            |--------------------------------------------------------------------------
            */

            orderNumberElement.textContent =
                '#' + orderNumber;


            /*
            |--------------------------------------------------------------------------
            | RESET LIST
            |--------------------------------------------------------------------------
            */

            list.innerHTML = '';


            /*
            |--------------------------------------------------------------------------
            | COUNTER
            |--------------------------------------------------------------------------
            */

            let completed = 0;
            let cancelled = 0;
            let processing = 0;
            let pending = 0;


            /*
            |--------------------------------------------------------------------------
            | RENDER SETIAP UNIT
            |--------------------------------------------------------------------------
            */

            units.forEach(function(unit) {

                let statusText = '';
                let statusClass = '';
                let icon = '';


                /*
                |--------------------------------------------------------------------------
                | SELESAI
                |--------------------------------------------------------------------------
                */

                if (unit.status === 'completed') {

                    completed++;

                    statusText = 'Selesai';

                    statusClass =
                        'bg-emerald-100 text-emerald-700';

                    icon =
                        '<i class="fa-solid fa-check mr-1"></i>';

                }


                /*
                |--------------------------------------------------------------------------
                | BAHAN HABIS
                |--------------------------------------------------------------------------
                */
                else if (unit.status === 'cancelled') {

                    cancelled++;

                    statusText = 'Bahan Habis';

                    statusClass =
                        'bg-rose-100 text-rose-700';

                    icon =
                        '<i class="fa-solid fa-box-open mr-1"></i>';

                }


                /*
                |--------------------------------------------------------------------------
                | DIPROSES
                |--------------------------------------------------------------------------
                */
                else if (unit.status === 'processing') {

                    processing++;

                    statusText = 'Diproses';

                    statusClass =
                        'bg-blue-100 text-blue-700';

                    icon =
                        '<i class="fa-solid fa-fire mr-1"></i>';

                }


                /*
                |--------------------------------------------------------------------------
                | MENUNGGU
                |--------------------------------------------------------------------------
                */
                else {

                    pending++;

                    statusText = 'Menunggu';

                    statusClass =
                        'bg-amber-100 text-amber-700';

                    icon =
                        '<i class="fa-regular fa-clock mr-1"></i>';

                }


                /*
                |--------------------------------------------------------------------------
                | BUAT ELEMENT
                |--------------------------------------------------------------------------
                */

                const item =
                    document.createElement('div');


                item.className =
                    'flex items-center justify-between ' +
                    'gap-3 rounded-xl border ' +
                    'border-slate-100 bg-slate-50 p-3';


                item.innerHTML = `

            <div class="flex items-center gap-3 min-w-0">

                <div
                    class="w-9 h-9
                           rounded-xl
                           bg-white
                           border border-slate-100
                           flex items-center
                           justify-center
                           text-slate-400
                           shrink-0"
                >
                    <i class="fa-solid fa-utensils"></i>
                </div>


                <div class="min-w-0">

                    <p
                        class="text-xs
                               font-black
                               text-slate-800
                               truncate"
                    >
                        ${escapeHtml(unit.menu)}
                    </p>

                    <p
                        class="text-[10px]
                               font-semibold
                               text-slate-400"
                    >
                        Unit ${escapeHtml(unit.unit)}
                    </p>

                </div>

            </div>


            <span
                class="px-2.5 py-1
                       rounded-lg
                       text-[10px]
                       font-black
                       whitespace-nowrap
                       ${statusClass}"
            >
                ${icon}
                ${statusText}
            </span>

        `;


                list.appendChild(item);

            });


            /*
            |--------------------------------------------------------------------------
            | RINGKASAN
            |--------------------------------------------------------------------------
            */

            const summaryParts = [];


            if (completed > 0) {

                summaryParts.push(
                    `${completed} selesai`
                );

            }


            if (cancelled > 0) {

                summaryParts.push(
                    `${cancelled} bahan habis`
                );

            }


            if (processing > 0) {

                summaryParts.push(
                    `${processing} diproses`
                );

            }


            if (pending > 0) {

                summaryParts.push(
                    `${pending} menunggu`
                );

            }


            summary.textContent =
                summaryParts.join(' • ');


            /*
            |--------------------------------------------------------------------------
            | TAMPILKAN MODAL
            |--------------------------------------------------------------------------
            */

            modal.classList.remove('hidden');

            modal.classList.add('flex');

        }


        /*
        |--------------------------------------------------------------------------
        | TUTUP MODAL
        |--------------------------------------------------------------------------
        */

        function closeOrderStatusModal() {

            const modal =
                document.getElementById(
                    'orderStatusModal'
                );


            if (!modal) {
                return;
            }


            modal.classList.add('hidden');

            modal.classList.remove('flex');

        }


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            const div =
                document.createElement('div');

            div.textContent =
                value ?? '';

            return div.innerHTML;

        }


        /*
        |--------------------------------------------------------------------------
        | KLIK DI LUAR MODAL
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function(event) {

                const modal =
                    document.getElementById(
                        'orderStatusModal'
                    );


                if (
                    modal &&
                    event.target === modal
                ) {

                    closeOrderStatusModal();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TOMBOL ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event) {

                if (
                    event.key === 'Escape'
                ) {

                    closeOrderStatusModal();

                }

            }
        );
    </script>

@endsection
