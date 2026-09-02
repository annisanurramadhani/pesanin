@extends('layouts.customer')

@section('content')

    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- =========================================================
        HEADER
    ========================================================== --}}
        <div class="text-center mb-6">

            @if (($order->payment_method === 'qris' || $order->payment_method === 'bank') && $order->payment_status === 'pending')
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-amber-50">
                    <i class="fa-solid fa-clock text-3xl text-amber-500"></i>
                </div>

                <h1 class="mt-4 text-2xl font-black text-slate-900">
                    Menunggu Pembayaran
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Silakan selesaikan pembayaran untuk memproses pesanan.
                </p>
            @elseif ($order->payment_status === 'paid')
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-emerald-50">
                    <i class="fa-solid fa-circle-check text-3xl text-emerald-500"></i>
                </div>

                <h1 class="mt-4 text-2xl font-black text-slate-900">
                    Pesanan Berhasil
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Pembayaran telah dikonfirmasi.
                </p>
            @elseif ($order->payment_status === 'expired')
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-red-50">
                    <i class="fa-solid fa-clock text-3xl text-red-500"></i>
                </div>

                <h1 class="mt-4 text-2xl font-black text-slate-900">
                    Pembayaran Kedaluwarsa
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Waktu pembayaran telah habis.
                </p>
            @elseif ($order->payment_status === 'failed')
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-red-50">
                    <i class="fa-solid fa-circle-xmark text-3xl text-red-500"></i>
                </div>

                <h1 class="mt-4 text-2xl font-black text-slate-900">
                    Pembayaran Gagal
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Pembayaran tidak dapat diproses.
                </p>
            @else
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-emerald-50">
                    <i class="fa-solid fa-circle-check text-3xl text-emerald-500"></i>
                </div>

                <h1 class="mt-4 text-2xl font-black text-slate-900">
                    Pesanan Berhasil
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Terima kasih telah melakukan pemesanan.
                </p>
            @endif

        </div>


        {{-- =========================================================
        PAYMENT JS DATA
    ========================================================== --}}
        <div id="payment-container" data-payment-status="{{ $order->payment_status }}"
            data-payment-method="{{ $order->payment_method }}"
            data-payment-expires-at="{{ $order->payment_expires_at ? $order->payment_expires_at->toISOString() : '' }}"
            data-payment-url="{{ route('customer.order.payment', [
                'code' => $qrCode->code,
                'orderNumber' => request()->route('orderNumber'),
            ]) }}">
        </div>


        {{-- =========================================================
        ORDER INFORMATION
    ========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200
               shadow-sm p-5 mb-5">

            <div class="flex items-center justify-between gap-4">

                <div>

                    <p class="text-xs font-medium text-slate-400
                           uppercase tracking-wider">
                        ID Pesanan
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-900">
                        {{ $order->order_number }}
                    </p>

                </div>

                <div class="text-right">

                    <p class="text-xs font-medium text-slate-400
                           uppercase tracking-wider">
                        Total
                    </p>

                    <p class="mt-1 text-lg font-black text-slate-900">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </p>

                </div>

            </div>

        </div>


        {{-- =========================================================
        ORDER ITEMS
    ========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200
               shadow-sm p-5 mb-5">

            <p class="text-sm font-bold text-slate-900 mb-4">
                Detail Pesanan
            </p>


            <div class="space-y-3">

                @foreach ($order->items as $item)
                    <div class="flex items-center justify-between
                           gap-4">

                        <div class="min-w-0">

                            <p class="text-sm font-semibold
                                   text-slate-700 truncate">
                                {{ $item->menu_name ?? ($item->name ?? 'Menu') }}
                            </p>

                            <p class="text-xs text-slate-400 mt-0.5">

                                {{ $item->quantity }} ×

                                Rp
                                {{ number_format($item->price, 0, ',', '.') }}

                            </p>

                        </div>


                        <p class="text-sm font-semibold
                               text-slate-800 whitespace-nowrap">

                            Rp
                            {{ number_format($item->subtotal, 0, ',', '.') }}

                        </p>

                    </div>
                @endforeach

            </div>


            <div class="border-t border-slate-100
                   mt-5 pt-4">

                <div class="flex items-center justify-between">

                    <span class="text-sm font-semibold
                           text-slate-500">
                        Total Pembayaran
                    </span>

                    <span class="text-lg font-black
                           text-slate-900">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
        PAYMENT STATUS
    ========================================================== --}}
        <div class="bg-white rounded-2xl
               border border-slate-200
               shadow-sm p-5 mb-5">

            <div class="flex items-center
                   justify-between gap-3">

                <div>

                    <p
                        class="text-xs font-medium
                           text-slate-400 uppercase
                           tracking-wider">
                        Status Pembayaran
                    </p>

                    <p class="mt-1 text-sm
                           font-bold text-slate-900">
                        @if ($order->payment_method === 'qris')
                            QRIS
                        @elseif ($order->payment_method === 'bank')
                            Virtual Account
                        @else
                            Tunai
                        @endif
                    </p>

                </div>


                {{-- STATUS BADGE --}}
                <div>

                    @if ($order->payment_status === 'pending')
                        <span
                            class="inline-flex items-center
                               gap-1.5 px-3 py-1.5
                               rounded-full bg-amber-50
                               text-amber-700 text-xs
                               font-bold">

                            <span class="w-1.5 h-1.5 rounded-full
                                   bg-amber-500"></span>

                            Menunggu Pembayaran

                        </span>
                    @elseif ($order->payment_status === 'paid')
                        <span
                            class="inline-flex items-center
                               gap-1.5 px-3 py-1.5
                               rounded-full bg-emerald-50
                               text-emerald-700 text-xs
                               font-bold">

                            <span class="w-1.5 h-1.5 rounded-full
                                   bg-emerald-500"></span>

                            Lunas

                        </span>
                    @elseif (in_array($order->payment_status, ['failed', 'expired']))
                        <span
                            class="inline-flex items-center
                               gap-1.5 px-3 py-1.5
                               rounded-full bg-red-50
                               text-red-700 text-xs
                               font-bold">

                            <span class="w-1.5 h-1.5 rounded-full
                                   bg-red-500"></span>

                            Gagal / Kedaluwarsa

                        </span>
                    @else
                        <span
                            class="inline-flex items-center
                               gap-1.5 px-3 py-1.5
                               rounded-full bg-emerald-50
                               text-emerald-700 text-xs
                               font-bold">

                            <span class="w-1.5 h-1.5 rounded-full
                                   bg-emerald-500"></span>

                            Tunai

                        </span>
                    @endif

                </div>

            </div>


            {{-- =====================================================
            PAYMENT TIMER
        ====================================================== --}}
            @if (
                ($order->payment_method === 'qris' || $order->payment_method === 'bank') &&
                    $order->payment_status === 'pending' &&
                    $order->payment_expires_at)
                <div id="payment-countdown-container"
                    class="mt-5 rounded-2xl
                       border border-amber-100
                       bg-amber-50 p-4">

                    <div class="flex items-center
                           justify-center gap-2">

                        <i class="fa-solid fa-clock
                               text-amber-500"></i>

                        <p id="payment-countdown-title"
                            class="text-sm font-bold
                               text-amber-800">
                            Selesaikan Pembayaran
                        </p>

                    </div>


                    <p id="payment-countdown"
                        class="mt-2 text-3xl
                           font-black tracking-wider
                           text-center text-amber-600">
                        --:--
                    </p>


                    <p id="payment-countdown-description"
                        class="mt-1 text-xs
                           text-center text-amber-600">
                        Waktu pembayaran tersisa.
                    </p>

                </div>
            @endif

        </div>


        {{-- =========================================================
            PAYMENT DETAIL
        ========================================================= --}}

        @if ($order->payment_method === 'qris')

            {{-- =====================================================
                QRIS PAYMENT
            ====================================================== --}}
            <div
                class="bg-white rounded-2xl
                    border border-slate-200
                    shadow-sm p-5 mb-5">

                <div class="border-t border-slate-100 pt-5">

                    {{-- TOTAL --}}
                    <div class="text-center">

                        <p
                            class="text-xs font-bold
                                uppercase tracking-wider
                                text-slate-400">
                            Total Pembayaran
                        </p>

                        <p
                            class="mt-1 text-3xl
                                font-black text-slate-900">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </p>

                    </div>


                    {{-- =================================================
                        QRIS EXPIRED
                    ================================================== --}}
                    @if ($order->payment_status === 'expired')

                        <div
                            class="mt-6 rounded-2xl
                                border border-red-100
                                bg-red-50 p-5 text-center">

                            <div
                                class="mx-auto flex h-14 w-14
                                    items-center justify-center
                                    rounded-full bg-red-100">
                                <i class="fa-solid fa-ban
                                    text-xl text-red-600"></i>
                            </div>

                            <p
                                class="mt-3 text-sm font-bold
                                    text-red-800">

                                Pembayaran Kedaluwarsa

                            </p>

                            <p
                                class="mt-1 text-xs
                                    leading-relaxed
                                    text-red-600">

                                Pembayaran sudah kedaluwarsa.
                                Waktu pembayaran telah habis.
                                Nomor QRIS ini sudah tidak dapat digunakan.

                            </p>

                        </div>


                    {{-- =================================================
                        QR CODE
                    ================================================== --}}
                    @elseif ($order->payment_status === 'pending')

                        <div class="mt-7 text-center">

                            <p class="text-sm font-bold text-slate-700">
                                Scan QR Code
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Gunakan aplikasi pembayaran
                                yang mendukung QRIS.
                            </p>


                            @if (!empty($payment['qr_code_url']))

                                <div
                                    class="mx-auto mt-6 flex w-fit
                                        items-center justify-center
                                        rounded-2xl border
                                        border-slate-200
                                        bg-white p-4 shadow-sm">

                                    <img
                                        src="{{ $payment['qr_code_url'] }}"
                                        alt="QRIS Pembayaran"
                                        class="h-56 w-56 object-contain"
                                    >

                                </div>

                            @else

                                <div
                                    class="mt-6 rounded-xl
                                        bg-red-50
                                        border border-red-100
                                        p-4">

                                    <p
                                        class="text-sm font-semibold
                                            text-red-700">
                                        QRIS tidak tersedia.
                                    </p>

                                </div>

                            @endif

                        </div>


                        {{-- =================================================
                            CARA PEMBAYARAN QRIS
                        ================================================== --}}
                        <div
                            class="mt-7 rounded-2xl
                                bg-slate-50 p-5 text-left">

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
                                        class="mt-2 space-y-1
                                            text-xs leading-5
                                            text-slate-500">

                                        <li>
                                            1. Buka aplikasi pembayaran
                                            yang mendukung QRIS.
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
                        QRIS PAID
                    ================================================== --}}
                    @elseif ($order->payment_status === 'paid')

                        <div
                            class="mt-6 rounded-2xl
                                border border-emerald-100
                                bg-emerald-50 p-6 text-center">

                            <div
                                class="mx-auto flex h-14 w-14
                                    items-center justify-center
                                    rounded-full bg-emerald-100">

                                <i
                                    class="fa-solid fa-check
                                        text-xl text-emerald-600">
                                </i>

                            </div>

                            <p
                                class="mt-3 text-sm font-bold
                                    text-emerald-800">
                                Pembayaran berhasil
                            </p>

                            <p
                                class="mt-1 text-xs
                                    text-emerald-600">
                                Pembayaran QRIS telah dikonfirmasi.
                            </p>

                        </div>


                    {{-- =================================================
                        QRIS FAILED
                    ================================================== --}}
                    @elseif ($order->payment_status === 'failed')

                        <div
                            class="mt-6 rounded-2xl
                                border border-red-100
                                bg-red-50 p-6 text-center">

                            <div
                                class="mx-auto flex h-14 w-14
                                    items-center justify-center
                                    rounded-full bg-red-100">

                                <i
                                    class="fa-solid fa-xmark
                                        text-xl text-red-600">
                                </i>

                            </div>

                            <p
                                class="mt-3 text-sm font-bold
                                    text-red-800">
                                Pembayaran Gagal
                            </p>

                            <p
                                class="mt-1 text-xs
                                    leading-relaxed
                                    text-red-600">
                                Pembayaran sudah gagal
                                atau tidak dapat diproses.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


        @elseif ($order->payment_method === 'bank')

            {{-- =====================================================
                BANK TRANSFER PAYMENT
            ====================================================== --}}
            <div
                class="bg-white rounded-2xl
                    border border-slate-200
                    shadow-sm p-5 mb-5">

                <div
                    class="border-t border-slate-100
                        mt-0 pt-5">

                    {{-- TOTAL --}}
                    <div class="text-center">

                        <p
                            class="text-xs font-bold
                                uppercase tracking-wider
                                text-slate-400">
                            Total Pembayaran
                        </p>

                        <p
                            class="mt-1 text-3xl
                                font-black text-slate-900">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </p>

                    </div>


                    {{-- =================================================
                        BANK EXPIRED
                    ================================================== --}}
                    @if ($order->payment_status === 'expired')

                        <div
                            class="mt-6 rounded-2xl
                                border border-red-100
                                bg-red-50 p-5 text-center">

                            <div
                                class="mx-auto flex h-14 w-14
                                    items-center justify-center
                                    rounded-full bg-red-100">
                                <i class="fa-solid fa-ban
                                    text-xl text-red-600"></i>
                            </div>

                            <p
                                class="mt-3 text-sm font-bold
                                    text-red-800">

                                Pembayaran Kedaluwarsa

                            </p>

                            <p
                                class="mt-1 text-xs
                                    leading-relaxed
                                    text-red-600">

                                Pembayaran sudah kedaluwarsa.
                                Waktu pembayaran telah habis.
                                Nomor Virtual Account ini
                                sudah tidak dapat digunakan.

                            </p>

                        </div>


                    {{-- =================================================
                        BANK INFORMATION
                    ================================================== --}}
                    @elseif ($order->payment_status === 'pending')

                        <div
                            class="mt-6 rounded-2xl
                                border border-indigo-100
                                bg-indigo-50 p-5">

                            <div class="text-center">

                                <div
                                    class="mx-auto flex h-12 w-12
                                        items-center justify-center
                                        rounded-full bg-indigo-100">

                                    <i
                                        class="fa-solid fa-building-columns
                                            text-xl text-indigo-600">
                                    </i>

                                </div>

                                <p
                                    class="mt-3 text-sm
                                        font-bold text-indigo-800">
                                    Virtual Account
                                </p>

                                <p
                                    class="mt-1 text-xs
                                        text-indigo-600">
                                    Silakan lakukan transfer
                                    sesuai nominal berikut.
                                </p>


                                {{-- BANK --}}
                                @if (!empty($payment['bank']))

                                    <div
                                        class="mt-5 rounded-xl
                                            bg-white
                                            border border-indigo-100
                                            p-4">

                                        <p class="text-xs text-slate-400">
                                            Bank
                                        </p>

                                        <p
                                            class="mt-1 text-lg
                                                font-black uppercase
                                                text-slate-900">
                                            {{ $payment['bank'] }}
                                        </p>

                                    </div>

                                @endif


                                {{-- VA NUMBER --}}
                                @if (!empty($payment['va_number']))

                                    <div
                                        class="mt-4 rounded-xl
                                            bg-white
                                            border border-indigo-100
                                            p-4">

                                        <p class="text-xs text-slate-400">
                                            Nomor Virtual Account
                                        </p>

                                        <div
                                            class="mt-2 flex
                                                items-center
                                                justify-between
                                                gap-3">

                                            <p
                                                id="va-number"
                                                class="text-xl
                                                    font-black
                                                    tracking-wider
                                                    text-slate-900
                                                    break-all">
                                                {{ $payment['va_number'] }}
                                            </p>

                                            <button
                                                type="button"
                                                onclick="copyVA()"
                                                class="shrink-0
                                                    inline-flex
                                                    items-center
                                                    justify-center
                                                    w-10 h-10
                                                    rounded-xl
                                                    bg-indigo-50
                                                    text-indigo-600
                                                    hover:bg-indigo-100
                                                    transition"
                                                title="Salin Nomor VA">

                                                <i class="fa-solid fa-copy"></i>

                                            </button>

                                        </div>

                                    </div>

                                @endif


                                {{-- NOMINAL --}}
                                <div
                                    class="mt-4 rounded-xl
                                        bg-white
                                        border border-indigo-100
                                        p-4">

                                    <div
                                        class="flex
                                            justify-between
                                            items-center">

                                        <span
                                            class="text-xs
                                                text-slate-500">
                                            Nominal Transfer
                                        </span>

                                        <span
                                            class="text-base
                                                font-black
                                                text-slate-900">

                                            Rp
                                            {{ number_format($order->total, 0, ',', '.') }}

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                            CARA PEMBAYARAN BANK
                        ================================================== --}}
                        <div
                            class="mt-5 rounded-2xl
                                bg-slate-50 p-5">

                            <div class="flex gap-3">

                                <i
                                    class="fa-solid fa-circle-info
                                        mt-0.5 text-indigo-500">
                                </i>

                                <div>

                                    <p class="text-sm font-bold text-slate-700">
                                        Cara Pembayaran
                                    </p>

                                    <ol
                                        class="mt-2 space-y-1
                                            text-xs leading-5
                                            text-slate-500">

                                        <li>
                                            1. Buka aplikasi mobile banking
                                            atau ATM.
                                        </li>

                                        <li>
                                            2. Pilih menu Transfer /
                                            Virtual Account.
                                        </li>

                                        <li>
                                            3. Masukkan nomor Virtual Account
                                            di atas.
                                        </li>

                                        <li>
                                            4. Pastikan nominal pembayaran
                                            sudah sesuai.
                                        </li>

                                        <li>
                                            5. Konfirmasi pembayaran.
                                        </li>

                                    </ol>

                                </div>

                            </div>

                        </div>


                    {{-- =================================================
                        BANK PAID
                    ================================================== --}}
                    @elseif ($order->payment_status === 'paid')

                        <div
                            class="mt-6 rounded-2xl
                                border border-emerald-100
                                bg-emerald-50 p-6 text-center">

                            <div
                                class="mx-auto flex h-14 w-14
                                    items-center justify-center
                                    rounded-full bg-emerald-100">

                                <i
                                    class="fa-solid fa-check
                                        text-xl text-emerald-600">
                                </i>

                            </div>

                            <p
                                class="mt-3 text-sm font-bold
                                    text-emerald-800">
                                Pembayaran berhasil
                            </p>

                            <p
                                class="mt-1 text-xs
                                    text-emerald-600">
                                Pembayaran transfer bank
                                telah dikonfirmasi.
                            </p>

                        </div>


                    {{-- =================================================
                        BANK FAILED
                    ================================================== --}}
                    @elseif ($order->payment_status === 'failed')

                        <div
                            class="mt-6 rounded-2xl
                                border border-red-100
                                bg-red-50 p-6 text-center">

                            <div
                                class="mx-auto flex h-14 w-14
                                    items-center justify-center
                                    rounded-full bg-red-100">

                                <i
                                    class="fa-solid fa-xmark
                                        text-xl text-red-600">
                                </i>

                            </div>

                            <p
                                class="mt-3 text-sm font-bold
                                    text-red-800">
                                Pembayaran Gagal
                            </p>

                            <p
                                class="mt-1 text-xs
                                    leading-relaxed
                                    text-red-600">
                                Pembayaran sudah gagal
                                atau tidak dapat diproses.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


        @elseif ($order->payment_method === 'cash')

            {{-- =========================================================
                CASH PAYMENT
            ========================================================== --}}
            <div
                class="bg-white rounded-2xl
                    border border-slate-200
                    shadow-sm p-5 mb-5">

                <div
                    class="mt-0 rounded-xl
                        bg-emerald-50
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

            </div>

        @endif


        {{-- =========================================================
            STATUS INFO
        ========================================================= --}}
        @if ($order->payment_method === 'qris' || $order->payment_method === 'bank')

            @if ($order->payment_status === 'pending')

                <div class="text-center mb-5">

                    <p class="text-xs text-slate-400">

                        <i class="fa-solid fa-rotate mr-1"></i>

                        Status pembayaran akan diperiksa otomatis.

                    </p>

                </div>

            @elseif ($order->payment_status === 'paid')

                <div class="text-center mb-5">

                    <p class="text-xs text-emerald-600">

                        <i class="fa-solid fa-circle-check mr-1"></i>

                        Pembayaran telah dikonfirmasi.

                    </p>

                </div>

            @elseif ($order->payment_status === 'failed')

                <div class="text-center mb-5">

                    <p class="text-xs text-red-600">

                        <i class="fa-solid fa-circle-xmark mr-1"></i>

                        Pembayaran gagal.

                    </p>

                </div>

            @endif

        @endif


        {{-- =========================================================
ACTION
========================================================== --}}
<div class="space-y-3">

    @if (
        $order->payment_status === 'expired' ||
        $order->payment_status === 'failed'
    )

        {{-- PEMBAYARAN GAGAL / EXPIRED --}}
        <a href="{{ route('customer.checkout', $qrCode->code) }}?retry_order={{ urlencode(Crypt::encryptString($order->order_number)) }}"
            class="flex items-center justify-center gap-2
                   w-full py-3
                   rounded-xl
                   bg-slate-900
                   text-white
                   text-sm font-bold
                   hover:bg-slate-800
                   transition">

            <i class="fa-solid fa-arrow-left"></i>

            Kembali ke Checkout

        </a>

    @else

        {{-- PEMBAYARAN BERHASIL / NORMAL --}}
        <a href="{{ route('customer.menu', $qrCode->code) }}"
            class="flex items-center justify-center gap-2
                   w-full py-3
                   rounded-xl
                   bg-slate-900
                   text-white
                   text-sm font-bold
                   hover:bg-slate-800
                   transition">

            <i class="fa-solid fa-utensils"></i>

            Kembali ke Menu

        </a>

    @endif

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

{{-- =========================================================
    PAYMENT JAVASCRIPT
========================================================== --}}
<script src="{{ asset('js/customer/order-success.js') }}"></script>
