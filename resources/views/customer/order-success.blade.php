@extends('layouts.customer')

@section('content')

<div class="max-w-lg mx-auto px-4 py-6">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="text-center mb-6">

        <div
            class="mx-auto flex h-16 w-16 items-center justify-center
                   rounded-full bg-emerald-50"
        >
            <i class="fa-solid fa-circle-check text-3xl text-emerald-500"></i>
        </div>

        <h1 class="mt-4 text-2xl font-black text-slate-900">
            Pesanan Berhasil
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Terima kasih telah melakukan pemesanan.
        </p>

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

        <div class="space-y-4">

            @foreach ($order->items as $item)

                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0">

                        <p class="text-sm font-semibold text-slate-800">
                            {{ $item->menu_name }}
                        </p>

                        <p class="text-xs text-slate-400 mt-1">

                            {{ $item->quantity }}
                            ×
                            Rp {{ number_format($item->price, 0, ',', '.') }}

                        </p>

                    </div>

                    <p class="text-sm font-bold text-slate-900
                              whitespace-nowrap">

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
    <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-5 mb-5">

        {{-- =====================================================
            PAYMENT HEADER
        ====================================================== --}}
        <div class="flex items-center justify-between gap-4">

            <div class="flex items-center gap-3">

                <div
                    class="w-11 h-11 rounded-xl
                           flex items-center justify-center
                           @if ($order->payment_method === 'qris')
                               bg-blue-50
                           @elseif ($order->payment_method === 'bank')
                               bg-indigo-50
                           @else
                               bg-emerald-50
                           @endif"
                >

                    @if ($order->payment_method === 'qris')

                        <i class="fa-solid fa-qrcode
                                  text-blue-600 text-lg"></i>

                    @elseif ($order->payment_method === 'bank')

                        <i class="fa-solid fa-building-columns
                                  text-indigo-600 text-lg"></i>

                    @else

                        <i class="fa-solid fa-money-bill-wave
                                  text-emerald-600 text-lg"></i>

                    @endif

                </div>


                <div>

                    <p class="text-xs font-medium text-slate-400
                              uppercase tracking-wider">

                        Metode Pembayaran

                    </p>

                    <p class="font-bold text-slate-900 mt-0.5">

                        @if ($order->payment_method === 'qris')

                            QRIS

                        @elseif ($order->payment_method === 'bank')

                            Transfer Bank

                        @else

                            Tunai

                        @endif

                    </p>

                </div>

            </div>


            {{-- =================================================
                PAYMENT STATUS
            ================================================== --}}
            @if (
                $order->payment_method === 'qris' ||
                $order->payment_method === 'bank'
            )

                @if ($order->payment_status === 'pending')

                    <span
                        class="inline-flex items-center gap-1.5
                               px-3 py-1.5 rounded-full
                               bg-amber-50 text-amber-700
                               text-xs font-bold"
                    >

                        <span
                            class="w-1.5 h-1.5 rounded-full
                                   bg-amber-500"
                        ></span>

                        Belum Dibayar

                    </span>

                @elseif ($order->payment_status === 'paid')

                    <span
                        class="inline-flex items-center gap-1.5
                               px-3 py-1.5 rounded-full
                               bg-emerald-50 text-emerald-700
                               text-xs font-bold"
                    >

                        <span
                            class="w-1.5 h-1.5 rounded-full
                                   bg-emerald-500"
                        ></span>

                        Lunas

                    </span>

                @elseif (
                    in_array(
                        $order->payment_status,
                        ['failed', 'expired']
                    )
                )

                    <span
                        class="inline-flex items-center gap-1.5
                               px-3 py-1.5 rounded-full
                               bg-red-50 text-red-700
                               text-xs font-bold"
                    >

                        <span
                            class="w-1.5 h-1.5 rounded-full
                                   bg-red-500"
                        ></span>

                        Gagal / Kedaluwarsa

                    </span>

                @endif

            @else

                <span
                    class="inline-flex items-center gap-1.5
                           px-3 py-1.5 rounded-full
                           bg-emerald-50 text-emerald-700
                           text-xs font-bold"
                >

                    <span
                        class="w-1.5 h-1.5 rounded-full
                               bg-emerald-500"
                    ></span>

                    Tunai

                </span>

            @endif

        </div>


        {{-- =========================================================
            QRIS PAYMENT
        ========================================================== --}}
        @if ($order->payment_method === 'qris')

            <div class="border-t border-slate-100 mt-5 pt-5">

                {{-- TOTAL --}}
                <div class="text-center">

                    <p class="text-xs font-bold uppercase
                              tracking-wider text-slate-400">

                        Total Pembayaran

                    </p>

                    <p class="mt-1 text-3xl font-black text-slate-900">

                        Rp {{ number_format($order->total, 0, ',', '.') }}

                    </p>

                </div>


                {{-- QR CODE --}}
                @if ($order->payment_status === 'pending')

                    <div class="mt-7 text-center">

                        <p class="text-sm font-bold text-slate-700">
                            Scan QR Code
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Gunakan aplikasi pembayaran yang mendukung QRIS.
                        </p>


                        @if (!empty($payment['qr_code_url']))

                            <div
                                class="mx-auto mt-6 flex w-fit
                                       items-center justify-center
                                       rounded-2xl border border-slate-100
                                       bg-white p-3 shadow-sm"
                            >

                                <img
                                    src="{{ $payment['qr_code_url'] }}"
                                    alt="QRIS Pembayaran"
                                    class="h-64 w-64 object-contain"
                                >

                            </div>

                        @else

                            <div
                                class="mt-6 rounded-xl
                                       border border-red-100
                                       bg-red-50 p-5"
                            >

                                <i
                                    class="fa-solid fa-triangle-exclamation
                                           text-2xl text-red-500"
                                ></i>

                                <p class="mt-2 text-sm font-semibold
                                          text-red-600">

                                    QRIS tidak tersedia.

                                </p>

                            </div>

                        @endif

                    </div>


                @elseif ($order->payment_status === 'paid')

                    <div
                        class="mt-6 rounded-2xl
                               border border-emerald-100
                               bg-emerald-50 p-6 text-center"
                    >

                        <div
                            class="mx-auto flex h-14 w-14
                                   items-center justify-center
                                   rounded-full bg-emerald-100"
                        >

                            <i
                                class="fa-solid fa-check
                                       text-xl text-emerald-600"
                            ></i>

                        </div>

                        <p class="mt-3 text-sm font-bold
                                  text-emerald-800">

                            Pembayaran berhasil

                        </p>

                        <p class="mt-1 text-xs text-emerald-600">

                            Pembayaran QRIS telah dikonfirmasi.

                        </p>

                    </div>

                @endif


                {{-- CARA PEMBAYARAN QRIS --}}
                @if ($order->payment_status === 'pending')

                    <div
                        class="mt-7 rounded-2xl
                               bg-slate-50 p-5 text-left"
                    >

                        <div class="flex gap-3">

                            <i
                                class="fa-solid fa-circle-info
                                       mt-0.5 text-amber-500"
                            ></i>

                            <div>

                                <p class="text-sm font-bold text-slate-700">
                                    Cara Pembayaran
                                </p>

                                <ol
                                    class="mt-2 space-y-1 text-xs
                                           leading-5 text-slate-500"
                                >

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

                @endif

            </div>


        {{-- =========================================================
            BANK TRANSFER PAYMENT
        ========================================================== --}}
        @elseif ($order->payment_method === 'bank')

            <div class="border-t border-slate-100 mt-5 pt-5">

                {{-- TOTAL --}}
                <div class="text-center">

                    <p class="text-xs font-bold uppercase
                              tracking-wider text-slate-400">

                        Total Pembayaran

                    </p>

                    <p class="mt-1 text-3xl font-black text-slate-900">

                        Rp {{ number_format($order->total, 0, ',', '.') }}

                    </p>

                </div>


                {{-- =================================================
                    BANK INFORMATION
                ================================================== --}}
                @if ($order->payment_status === 'pending')

                    <div
                        class="mt-6 rounded-2xl
                               border border-indigo-100
                               bg-indigo-50 p-5"
                    >

                        <div class="text-center">

                            <div
                                class="mx-auto flex h-12 w-12
                                       items-center justify-center
                                       rounded-xl bg-white"
                            >

                                <i
                                    class="fa-solid fa-building-columns
                                           text-indigo-600 text-xl"
                                ></i>

                            </div>


                            <p class="mt-3 text-xs font-semibold
                                      uppercase tracking-wider
                                      text-indigo-400">

                                Transfer ke Bank

                            </p>


                            <p class="mt-1 text-xl font-black
                                      text-indigo-900 uppercase">

                                {{ $order->bank ?? '-' }}

                            </p>

                        </div>


                        {{-- VA NUMBER --}}
                        <div class="mt-5">

                            <p class="text-xs font-semibold
                                      text-slate-500 text-center">

                                Nomor Virtual Account

                            </p>


                            @if (!empty($order->va_number))

                                <div
                                    class="mt-2 flex items-center
                                           justify-between gap-3
                                           rounded-xl border
                                           border-indigo-100
                                           bg-white p-4"
                                >

                                    <p
                                        id="va-number"
                                        class="text-lg font-black
                                               tracking-wider
                                               text-slate-900"
                                    >

                                        {{ $order->va_number }}

                                    </p>


                                    <button
                                        type="button"
                                        onclick="copyVA()"
                                        class="flex h-10 w-10
                                               flex-shrink-0
                                               items-center justify-center
                                               rounded-lg bg-slate-900
                                               text-white
                                               hover:bg-slate-800
                                               transition"
                                        title="Salin nomor VA"
                                    >

                                        <i
                                            class="fa-solid fa-copy"
                                        ></i>

                                    </button>

                                </div>

                            @else

                                <div
                                    class="mt-2 rounded-xl
                                           border border-red-100
                                           bg-red-50 p-4 text-center"
                                >

                                    <i
                                        class="fa-solid
                                               fa-triangle-exclamation
                                               text-red-500"
                                    ></i>

                                    <p
                                        class="mt-2 text-xs
                                               font-semibold
                                               text-red-600"
                                    >

                                        Nomor Virtual Account belum tersedia.

                                    </p>

                                </div>

                            @endif

                        </div>


                        {{-- NOMINAL --}}
                        <div
                            class="mt-4 rounded-xl bg-white
                                   border border-indigo-100 p-4"
                        >

                            <div class="flex justify-between items-center">

                                <span class="text-xs text-slate-500">
                                    Nominal Transfer
                                </span>

                                <span class="text-base font-black
                                             text-slate-900">

                                    Rp {{ number_format($order->total, 0, ',', '.') }}

                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        CARA PEMBAYARAN BANK
                    ================================================== --}}
                    <div
                        class="mt-5 rounded-2xl
                               bg-slate-50 p-5"
                    >

                        <div class="flex gap-3">

                            <i
                                class="fa-solid fa-circle-info
                                       mt-0.5 text-indigo-500"
                            ></i>

                            <div>

                                <p class="text-sm font-bold
                                          text-slate-700">

                                    Cara Pembayaran

                                </p>

                                <ol
                                    class="mt-2 space-y-1 text-xs
                                           leading-5 text-slate-500"
                                >

                                    <li>
                                        1. Buka aplikasi mobile banking
                                        atau ATM.
                                    </li>

                                    <li>
                                        2. Pilih menu Transfer / Virtual Account.
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


                @elseif ($order->payment_status === 'paid')

                    {{-- =================================================
                        BANK PAID
                    ================================================== --}}
                    <div
                        class="mt-6 rounded-2xl
                               border border-emerald-100
                               bg-emerald-50 p-6 text-center"
                    >

                        <div
                            class="mx-auto flex h-14 w-14
                                   items-center justify-center
                                   rounded-full bg-emerald-100"
                        >

                            <i
                                class="fa-solid fa-check
                                       text-xl text-emerald-600"
                            ></i>

                        </div>

                        <p class="mt-3 text-sm font-bold
                                  text-emerald-800">

                            Pembayaran berhasil

                        </p>

                        <p class="mt-1 text-xs text-emerald-600">

                            Pembayaran transfer bank telah dikonfirmasi.

                        </p>

                    </div>


                @elseif (
                    in_array(
                        $order->payment_status,
                        ['failed', 'expired']
                    )
                )

                    <div
                        class="mt-6 rounded-2xl
                               border border-red-100
                               bg-red-50 p-6 text-center"
                    >

                        <div
                            class="mx-auto flex h-14 w-14
                                   items-center justify-center
                                   rounded-full bg-red-100"
                        >

                            <i
                                class="fa-solid fa-xmark
                                       text-xl text-red-600"
                            ></i>

                        </div>

                        <p class="mt-3 text-sm font-bold
                                  text-red-800">

                            Pembayaran tidak berhasil

                        </p>

                        <p class="mt-1 text-xs text-red-600">

                            Pembayaran sudah gagal atau
                            Virtual Account telah kedaluwarsa.

                        </p>

                    </div>

                @endif


        {{-- =========================================================
            CASH PAYMENT
        ========================================================== --}}
        @else

            <div
                class="mt-5 rounded-xl
                       bg-emerald-50
                       border border-emerald-100 p-4"
            >

                <div class="flex gap-3">

                    <i
                        class="fa-solid fa-circle-info
                               text-emerald-500 mt-0.5"
                    ></i>

                    <div>

                        <p class="text-sm font-semibold
                                  text-emerald-800">

                            Pembayaran Tunai

                        </p>

                        <p class="text-xs text-emerald-600
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
        STATUS INFO
    ========================================================== --}}
    @if (
        $order->payment_method === 'qris' ||
        $order->payment_method === 'bank'
    )

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

        @endif

    @endif


    {{-- =========================================================
        ACTION
    ========================================================== --}}
    <div class="space-y-3">

        <a
            href="{{ route('customer.menu', $qrCode->code) }}"
            class="flex items-center justify-center gap-2
                   w-full py-3.5 rounded-xl bg-slate-900
                   text-white text-sm font-bold
                   hover:bg-slate-800 transition"
        >

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


{{-- =========================================================
    COPY VA
========================================================== --}}
@if ($order->payment_method === 'bank')

<script>

    function copyVA() {

        const vaElement =
            document.getElementById('va-number');

        if (!vaElement) {
            return;
        }

        const va =
            vaElement.innerText.trim();


        navigator.clipboard.writeText(va)
            .then(function () {

                alert('Nomor Virtual Account berhasil disalin.');

            })
            .catch(function () {

                alert('Gagal menyalin nomor Virtual Account.');

            });
    }

</script>

@endif


{{-- =========================================================
    AUTO CHECK PAYMENT STATUS
========================================================== --}}
@if (
    (
        $order->payment_method === 'qris' ||
        $order->payment_method === 'bank'
    )
    &&
    $order->payment_status === 'pending'
)

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const checkInterval =
                setInterval(
                    function () {

                        /*
                        |--------------------------------------------------------------------------
                        | PANGGIL ENDPOINT PAYMENT
                        |--------------------------------------------------------------------------
                        */

                        fetch(
                            "{{ route(
                                'customer.order.payment',
                                [
                                    'code' => $qrCode->code,
                                    'orderNumber' => request()->route('orderNumber')
                                ]
                            ) }}",
                            {
                                method: 'GET',

                                headers: {
                                    'X-Requested-With':
                                        'XMLHttpRequest',

                                    'Accept':
                                        'application/json'
                                },

                                cache:
                                    'no-store'
                            }
                        )
                        .then(
                            response => {

                                if (!response.ok) {
                                    throw new Error(
                                        'HTTP error ' +
                                        response.status
                                    );
                                }

                                return response.json();
                            }
                        )
                        .then(
                            data => {

                                /*
                                |--------------------------------------------------------------------------
                                | PEMBAYARAN BERHASIL
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    data.success &&
                                    data.payment_status === 'paid'
                                ) {

                                    clearInterval(
                                        checkInterval
                                    );

                                    window.location.reload();

                                    return;
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | PEMBAYARAN GAGAL / EXPIRED
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    data.success &&
                                    (
                                        data.payment_status === 'failed' ||
                                        data.payment_status === 'expired'
                                    )
                                ) {

                                    clearInterval(
                                        checkInterval
                                    );

                                    window.location.reload();

                                }

                            }
                        )
                        .catch(
                            error => {

                                console.error(
                                    'Gagal mengecek status pembayaran:',
                                    error
                                );

                            }
                        );

                    },
                    5000
                );

        }
    );

</script>

@endif

@endsection