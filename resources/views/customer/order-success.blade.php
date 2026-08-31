@extends('layouts.customer')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- =========================================================
            SUCCESS HEADER
        ========================================================== --}}
        <div class="text-center mb-8">

            @if ($order->payment_method === 'qris' && $order->status === 'pending')
                <div
                    class="mx-auto w-16 h-16 rounded-2xl bg-amber-100
                    flex items-center justify-center mb-4">

                    <i class="fa-solid fa-qrcode text-2xl text-amber-600"></i>

                </div>

                <h1 class="text-2xl font-extrabold text-slate-900">
                    Pesanan Menunggu Pembayaran
                </h1>

                <p class="text-sm text-slate-500 mt-2">
                    Silakan scan QRIS di bawah untuk menyelesaikan pembayaran.
                </p>
            @else
                <div
                    class="mx-auto w-16 h-16 rounded-2xl bg-emerald-100
                    flex items-center justify-center mb-4">

                    <i class="fa-solid fa-check text-2xl text-emerald-600"></i>

                </div>

                <h1 class="text-2xl font-extrabold text-slate-900">
                    Pesanan Berhasil Dibuat
                </h1>

                <p class="text-sm text-slate-500 mt-2">
                    Terima kasih, pesanan kamu sudah kami terima.
                </p>
            @endif

        </div>


        {{-- =========================================================
            ORDER NUMBER + STATUS
        ========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">
                        Nomor Pesanan
                    </p>

                    <p class="text-xl font-extrabold text-slate-900 mt-1">
                        #{{ $order->order_number }}
                    </p>
                </div>

                <div class="text-right">

                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">
                        Status
                    </p>

                    @php
                        $statusClass = match ($order->status) {
                            'pending' => 'bg-amber-100 text-amber-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp

                    <span
                        class="inline-flex items-center mt-1 px-3 py-1 rounded-full
                        text-xs font-bold {{ $statusClass }}">

                        {{ ucfirst($order->status) }}

                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
            CUSTOMER INFORMATION
        ========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

            <div class="flex items-center gap-3 mb-5">

                <div
                    class="w-10 h-10 rounded-xl bg-slate-100
                    flex items-center justify-center">

                    <i class="fa-solid fa-user text-slate-500"></i>

                </div>

                <div>

                    <h2 class="font-bold text-slate-900">
                        Data Pemesan
                    </h2>

                    <p class="text-xs text-slate-400">
                        Informasi pesanan
                    </p>

                </div>

            </div>


            <div class="space-y-4">

                {{-- NAME --}}
                <div class="flex items-center justify-between gap-4">

                    <span class="text-sm text-slate-500">
                        Nama
                    </span>

                    <span class="text-sm font-semibold text-slate-800">
                        {{ $order->customer_name }}
                    </span>

                </div>


                {{-- PHONE --}}
                @if ($order->customer_phone)

                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Nomor Telepon
                        </span>

                        <span class="text-sm font-semibold text-slate-800">
                            {{ $order->customer_phone }}
                        </span>

                    </div>

                @endif


                {{-- EMAIL --}}
                @if ($order->customer_email)

                    <div class="flex items-center justify-between gap-4">

                        <span class="text-sm text-slate-500">
                            Email
                        </span>

                        <span
                            class="text-sm font-semibold text-slate-800
                            break-all text-right">

                            {{ $order->customer_email }}

                        </span>

                    </div>

                @endif

            </div>

        </div>


        {{-- =========================================================
            ORDER ITEMS
        ========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

            <div class="flex items-center gap-3 mb-5">

                <div
                    class="w-10 h-10 rounded-xl bg-amber-100
                    flex items-center justify-center">

                    <i class="fa-solid fa-receipt text-amber-600"></i>

                </div>

                <div>

                    <h2 class="font-bold text-slate-900">
                        Detail Pesanan
                    </h2>

                    <p class="text-xs text-slate-400">
                        Menu yang kamu pesan
                    </p>

                </div>

            </div>


            <div class="space-y-4">

                @foreach ($order->items as $item)

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="font-semibold text-sm text-slate-800">
                                {{ $item->menu_name }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">

                                {{ $item->quantity }}
                                ×
                                Rp {{ number_format($item->price, 0, ',', '.') }}

                            </p>

                        </div>

                        <p class="text-sm font-bold text-slate-900 whitespace-nowrap">

                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}

                        </p>

                    </div>

                @endforeach

            </div>


            {{-- TOTAL --}}
            <div class="border-t border-slate-100 mt-5 pt-5 space-y-3">

                <div class="flex justify-between text-sm">

                    <span class="text-slate-500">
                        Subtotal
                    </span>

                    <span class="font-medium text-slate-800">

                        Rp {{ number_format($order->subtotal, 0, ',', '.') }}

                    </span>

                </div>


                <div class="flex justify-between items-center pt-2">

                    <span class="font-semibold text-slate-700">
                        Total
                    </span>

                    <span class="text-xl font-extrabold text-slate-900">

                        Rp {{ number_format($order->total, 0, ',', '.') }}

                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
            PAYMENT
        ========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">

            {{-- PAYMENT HEADER --}}
            <div class="flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">

                    <div
                        class="w-11 h-11 rounded-xl bg-blue-50
                        flex items-center justify-center">

                        @if ($order->payment_method === 'qris')

                            <i class="fa-solid fa-qrcode text-blue-600 text-lg"></i>

                        @else

                            <i class="fa-solid fa-money-bill-wave
                                text-emerald-600 text-lg"></i>

                        @endif

                    </div>


                    <div>

                        <p
                            class="text-xs font-medium text-slate-400
                            uppercase tracking-wider">

                            Metode Pembayaran

                        </p>

                        <p class="font-bold text-slate-900 mt-0.5">

                            {{ $order->payment_method === 'qris'
                                ? 'QRIS'
                                : 'Tunai' }}

                        </p>

                    </div>

                </div>


                {{-- PAYMENT STATUS --}}
                @if ($order->payment_method === 'qris')

                    @if ($order->status === 'pending')

                        <span
                            class="inline-flex items-center gap-1.5
                            px-3 py-1.5 rounded-full
                            bg-amber-50 text-amber-700
                            text-xs font-bold">

                            <span
                                class="w-1.5 h-1.5 rounded-full
                                bg-amber-500">
                            </span>

                            Belum Dibayar

                        </span>

                    @elseif ($order->status === 'processing')

                        <span
                            class="inline-flex items-center gap-1.5
                            px-3 py-1.5 rounded-full
                            bg-blue-50 text-blue-700
                            text-xs font-bold">

                            <span
                                class="w-1.5 h-1.5 rounded-full
                                bg-blue-500">
                            </span>

                            Pembayaran Berhasil

                        </span>

                    @elseif ($order->status === 'completed')

                        <span
                            class="inline-flex items-center gap-1.5
                            px-3 py-1.5 rounded-full
                            bg-emerald-50 text-emerald-700
                            text-xs font-bold">

                            <span
                                class="w-1.5 h-1.5 rounded-full
                                bg-emerald-500">
                            </span>

                            Lunas

                        </span>

                    @endif

                @endif

            </div>


            {{-- =====================================================
                QRIS PAYMENT
            ====================================================== --}}
            @if ($order->payment_method === 'qris')

                

                    {{-- =================================================
                        INFORMASI PEMBAYARAN
                    ================================================== --}}
                    <div class="border-b border-slate-200 px-6 py-5">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Total Pembayaran
                                </p>

                                <p class="mt-1 text-3xl font-black text-slate-900">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </p>

                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-wallet text-xl"></i>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        QR CODE
                    ================================================== --}}
                    <div class="px-6 py-8">

                        <div class="text-center">

                            <p class="text-sm font-bold text-slate-700">
                                Scan QR Code
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Gunakan GoPay atau aplikasi pembayaran lain
                                yang mendukung QRIS.
                            </p>


                            {{-- =================================================
                                QR
                            ================================================== --}}
                            @if (
                                $order->status === 'pending' &&
                                !empty($payment['qr_code_url'])
                            )

                                <div
                                    class="mx-auto mt-6 flex w-fit items-center justify-center rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                    <img
                                        src="{{ $payment['qr_code_url'] }}"
                                        alt="QRIS Pembayaran"
                                        class="h-64 w-64 object-contain"
                                    >

                                </div>

                            @elseif (
                                $order->status === 'pending' &&
                                empty($payment['qr_code_url'])
                            )

                                <div
                                    class="mt-6 rounded-xl border border-red-100 bg-white p-5">

                                    <i
                                        class="fa-solid fa-triangle-exclamation
                                        text-2xl text-red-500">
                                    </i>

                                    <p class="mt-2 text-sm text-red-600">
                                        QRIS tidak tersedia.
                                    </p>

                                </div>

                            @elseif ($order->status === 'processing')

                                <div
                                    class="mt-6 rounded-2xl border border-blue-100 bg-white p-6">

                                    <div
                                        class="mx-auto flex h-14 w-14 items-center
                                        justify-center rounded-full bg-blue-100">

                                        <i
                                            class="fa-solid fa-check
                                            text-xl text-blue-600">
                                        </i>

                                    </div>

                                    <p
                                        class="mt-3 text-sm font-bold text-blue-700">

                                        Pembayaran Berhasil

                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-500">

                                        Pesanan kamu sedang diproses.

                                    </p>

                                </div>

                            @endif


                            {{-- =================================================
                                STATUS
                            ================================================== --}}
                            @if ($order->status === 'pending')

                                <div
                                    class="mx-auto mt-6 flex max-w-sm items-center
                                    justify-center gap-2 rounded-xl bg-amber-50
                                    px-4 py-3 text-sm font-semibold text-amber-700">

                                    <i class="fa-solid fa-clock"></i>

                                    <span>
                                        Menunggu pembayaran
                                    </span>

                                </div>

                            @elseif ($order->status === 'processing')

                                <div
                                    class="mx-auto mt-6 flex max-w-sm items-center
                                    justify-center gap-2 rounded-xl bg-blue-50
                                    px-4 py-3 text-sm font-semibold text-blue-700">

                                    <i class="fa-solid fa-check"></i>

                                    <span>
                                        Pembayaran berhasil
                                    </span>

                                </div>

                            @elseif ($order->status === 'completed')

                                <div
                                    class="mx-auto mt-6 flex max-w-sm items-center
                                    justify-center gap-2 rounded-xl bg-emerald-50
                                    px-4 py-3 text-sm font-semibold text-emerald-700">

                                    <i class="fa-solid fa-circle-check"></i>

                                    <span>
                                        Pembayaran berhasil
                                    </span>

                                </div>

                            @endif


                            {{-- =================================================
                                INFORMASI / CARA PEMBAYARAN
                            ================================================== --}}
                            <div class="mt-7 rounded-2xl bg-slate-50 p-5 text-left">

                                <div class="flex gap-3">

                                    <i
                                        class="fa-solid fa-circle-info
                                        mt-0.5 text-amber-500">
                                    </i>

                                    <div>

                                        <p class="text-sm font-bold text-slate-700">
                                            Cara Pembayaran
                                        </p>

                                        <ol
                                            class="mt-2 space-y-1 text-xs
                                            leading-5 text-slate-500">

                                            <li>
                                                1. Buka aplikasi pembayaran yang mendukung QRIS.
                                            </li>

                                            <li>
                                                2. Pilih menu Scan QR.
                                            </li>

                                            <li>
                                                3. Scan QR Code di atas.
                                            </li>

                                            <li>
                                                4. Periksa nominal pembayaran.
                                            </li>

                                            <li>
                                                5. Konfirmasi pembayaran.
                                            </li>

                                        </ol>

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                                ORDER ID
                            ================================================== --}}
                            <div class="mt-5 text-center">

                                <p class="text-xs text-slate-400">
                                    ID Pesanan
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-600">
                                    {{ $order->order_number }}
                                </p>

                            </div>


                            {{-- =================================================
                                STATUS CHECK INFO
                            ================================================== --}}
                            <div class="mt-5 text-center">

                                <p class="text-xs text-slate-400">
                                    Status pembayaran akan diperiksa otomatis.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            {{-- =====================================================
                CASH PAYMENT
            ====================================================== --}}
            @else

                <div
                    class="mt-5 rounded-xl bg-emerald-50
                    border border-emerald-100 p-4">

                    <div class="flex gap-3">

                        <i
                            class="fa-solid fa-circle-info
                            text-emerald-500 mt-0.5">
                        </i>

                        <div>

                            <p
                                class="text-sm font-semibold
                                text-emerald-800">

                                Pembayaran Tunai

                            </p>

                            <p
                                class="text-xs text-emerald-600
                                mt-1 leading-relaxed">

                                Silakan lakukan pembayaran secara tunai
                                kepada kasir saat pesanan diproses.

                            </p>

                        </div>

                    </div>

                </div>

            @endif

        </div>


        {{-- =========================================================
            ACTION
        ========================================================== --}}
        <div class="space-y-3">

            <a
                href="{{ route('customer.menu', $qrCode->code) }}"
                class="flex items-center justify-center gap-2
                w-full py-3.5 rounded-xl bg-slate-900
                text-white text-sm font-bold
                hover:bg-slate-800 transition">

                <i class="fa-solid fa-utensils"></i>

                Kembali ke Menu

            </a>

        </div>


        {{-- =========================================================
            FOOTER
        ========================================================== --}}
        <div class="text-center mt-8">

            <p class="text-xs text-slate-400">

                {{ $merchant->name ?? 'PesanIn' }}

            </p>

            <p class="text-[11px] text-slate-300 mt-1">

                Terima kasih telah memesan melalui PesanIn

            </p>

        </div>

    </div>
@endsection
