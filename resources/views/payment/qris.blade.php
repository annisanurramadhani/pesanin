@extends('layouts.app')

@section('body')

<div class="min-h-screen bg-slate-50 px-5 py-10">

    <div class="mx-auto max-w-lg">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="mb-8 text-center">

            <div
                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500 text-2xl text-slate-950 shadow-lg shadow-amber-500/30">

                <i class="fa-solid fa-qrcode"></i>

            </div>


            <h1 class="text-2xl font-black text-slate-900">

                Pembayaran QRIS

            </h1>


            <p class="mt-2 text-sm text-slate-500">

                Scan QRIS untuk menyelesaikan pembayaran langganan Anda.

            </p>

        </div>


        {{-- =========================================================
            CARD
        ========================================================== --}}
        <div
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">


            {{-- =====================================================
                INFORMASI PEMBAYARAN
            ====================================================== --}}
            <div class="border-b border-slate-200 px-6 py-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-wider text-slate-400">

                            Total Pembayaran

                        </p>


                        <p class="mt-1 text-3xl font-black text-slate-900">

                            Rp
                            {{ number_format($subscription->price, 0, ',', '.') }}

                        </p>

                    </div>


                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                        <i class="fa-solid fa-wallet text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                QR CODE
            ====================================================== --}}
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
                    <div
                        class="mx-auto mt-6 flex w-fit items-center justify-center rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                        <img
                            src="{{ $qrUrl }}"
                            alt="QRIS Pembayaran"
                            class="h-64 w-64 object-contain"
                        >

                    </div>


                    {{-- =================================================
                        STATUS
                    ================================================== --}}
                    <div
                        id="payment-status"
                        class="mx-auto mt-6 flex max-w-sm items-center justify-center gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">

                        <i
                            id="payment-status-icon"
                            class="fa-solid fa-clock">
                        </i>


                        <span id="payment-status-text">

                            Menunggu pembayaran

                        </span>

                    </div>


                </div>


                {{-- =====================================================
                    INFORMASI
                ====================================================== --}}
                <div class="mt-7 rounded-2xl bg-slate-50 p-5">

                    <div class="flex gap-3">

                        <i
                            class="fa-solid fa-circle-info mt-0.5 text-amber-500">
                        </i>


                        <div>

                            <p class="text-sm font-bold text-slate-700">

                                Cara Pembayaran

                            </p>


                            <ol
                                class="mt-2 space-y-1 text-xs leading-5 text-slate-500">

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


                {{-- =====================================================
                    ORDER ID
                ====================================================== --}}
                <div class="mt-5 text-center">

                    <p class="text-xs text-slate-400">

                        ID Pesanan

                    </p>


                    <p
                        class="mt-1 text-xs font-bold text-slate-600">

                        {{ $orderId }}

                    </p>

                </div>


                {{-- =====================================================
                    STATUS CHECK INFO
                ====================================================== --}}
                <div class="mt-5 text-center">

                    <p
                        id="checking-text"
                        class="text-xs text-slate-400">

                        Status pembayaran akan diperiksa otomatis.

                    </p>

                </div>

            </div>

        </div>


        {{-- =========================================================
            KEMBALI
        ========================================================== --}}
        <div class="mt-6 text-center">

            <a
                href="{{ route('dashboard') }}"
                id="back-dashboard"
                class="text-sm font-bold text-slate-500 underline transition hover:text-slate-900">

                Kembali ke Dashboard

            </a>

        </div>

    </div>

</div>


{{-- ================================================================
    SWEETALERT
================================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    const encryptedSubscription =
        @json(encryptId($subscription->id));


    const statusUrl =
        @json(
            route(
                'public.subscription.payment.qris.status',
                encryptId($subscription->id)
            )
        );


    const dashboardUrl =
        @json(route('dashboard'));


    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const statusBox =
        document.getElementById('payment-status');


    const statusIcon =
        document.getElementById('payment-status-icon');


    const statusText =
        document.getElementById('payment-status-text');


    const checkingText =
        document.getElementById('checking-text');


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let checkingPayment = true;

    let paymentCompleted = false;

    let checkingInterval = null;


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS UI
    |--------------------------------------------------------------------------
    */

    function updateStatus(
        type,
        icon,
        text
    ) {


        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        statusBox.classList.remove(
            'bg-amber-50',
            'text-amber-700',
            'bg-emerald-50',
            'text-emerald-700',
            'bg-red-50',
            'text-red-700',
            'bg-blue-50',
            'text-blue-700'
        );


        /*
        |--------------------------------------------------------------------------
        | Amber
        |--------------------------------------------------------------------------
        */

        if (type === 'waiting') {

            statusBox.classList.add(
                'bg-amber-50',
                'text-amber-700'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Blue
        |--------------------------------------------------------------------------
        */

        if (type === 'processing') {

            statusBox.classList.add(
                'bg-blue-50',
                'text-blue-700'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Green
        |--------------------------------------------------------------------------
        */

        if (type === 'success') {

            statusBox.classList.add(
                'bg-emerald-50',
                'text-emerald-700'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Red
        |--------------------------------------------------------------------------
        */

        if (type === 'failed') {

            statusBox.classList.add(
                'bg-red-50',
                'text-red-700'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Icon
        |--------------------------------------------------------------------------
        */

        statusIcon.className =
            'fa-solid ' + icon;


        /*
        |--------------------------------------------------------------------------
        | Text
        |--------------------------------------------------------------------------
        */

        statusText.textContent =
            text;

    }


    /*
    |--------------------------------------------------------------------------
    | CHECK PAYMENT STATUS
    |--------------------------------------------------------------------------
    */

    async function checkPaymentStatus() {


        /*
        |--------------------------------------------------------------------------
        | Stop Jika Sudah Berhasil
        |--------------------------------------------------------------------------
        */

        if (
            !checkingPayment ||
            paymentCompleted
        ) {

            return;

        }


        try {


            checkingText.textContent =
                'Memeriksa status pembayaran...';


            const response =
                await fetch(
                    statusUrl,
                    {

                        method: 'GET',

                        headers: {

                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest',

                        },

                        credentials:
                            'same-origin',

                        cache:
                            'no-store',

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Response Tidak OK
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    'Gagal mendapatkan status pembayaran.'
                );

            }


            const data =
                await response.json();


            console.log(
                'QRIS Status:',
                data
            );


            /*
            |--------------------------------------------------------------------------
            | PEMBAYARAN BERHASIL
            |--------------------------------------------------------------------------
            */

            if (
                data.paid === true
                &&
                data.success === true
            ) {


                /*
                |--------------------------------------------------------------------------
                | Stop Polling
                |--------------------------------------------------------------------------
                */

                paymentCompleted =
                    true;

                checkingPayment =
                    false;


                if (checkingInterval) {

                    clearInterval(
                        checkingInterval
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Update UI
                |--------------------------------------------------------------------------
                */

                updateStatus(
                    'success',
                    'fa-circle-check',
                    'Pembayaran berhasil'
                );


                checkingText.textContent =
                    'Pembayaran berhasil. Mengarahkan ke dashboard...';


                /*
                |--------------------------------------------------------------------------
                | SweetAlert
                |--------------------------------------------------------------------------
                */

                Swal.fire({

                    icon: 'success',

                    title: 'Pembayaran Berhasil!',

                    text:
                        'Langganan Anda berhasil diperpanjang dan sudah aktif.',

                    confirmButtonText:
                        'Lanjutkan',

                    confirmButtonColor:
                        '#f59e0b',

                    allowOutsideClick:
                        false,

                    allowEscapeKey:
                        false,

                }).then(function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Dashboard
                    |--------------------------------------------------------------------------
                    */

                    window.location.href =
                        data.redirect
                        || dashboardUrl;

                });


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | PENDING
            |--------------------------------------------------------------------------
            */

            if (
                data.status === 'pending'
                ||
                data.paid === false
            ) {

                updateStatus(
                    'waiting',
                    'fa-clock',
                    'Menunggu pembayaran'
                );


                checkingText.textContent =
                    'Menunggu konfirmasi pembayaran...';


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | EXPIRED
            |--------------------------------------------------------------------------
            */

            if (
                data.status === 'expire'
            ) {

                checkingPayment =
                    false;


                if (checkingInterval) {

                    clearInterval(
                        checkingInterval
                    );

                }


                updateStatus(
                    'failed',
                    'fa-circle-xmark',
                    'Pembayaran kedaluwarsa'
                );


                checkingText.textContent =
                    'QRIS sudah tidak dapat digunakan.';


                Swal.fire({

                    icon: 'warning',

                    title: 'Pembayaran Kedaluwarsa',

                    text:
                        'QRIS ini sudah kedaluwarsa. Silakan kembali dan buat pembayaran baru.',

                    confirmButtonText:
                        'Kembali',

                    confirmButtonColor:
                        '#f59e0b'

                }).then(function () {

                    window.location.href =
                        dashboardUrl;

                });


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | CANCEL / DENY
            |--------------------------------------------------------------------------
            */

            if (
                data.status === 'cancel'
                ||
                data.status === 'deny'
            ) {

                checkingPayment =
                    false;


                if (checkingInterval) {

                    clearInterval(
                        checkingInterval
                    );

                }


                updateStatus(
                    'failed',
                    'fa-circle-xmark',
                    'Pembayaran gagal'
                );


                checkingText.textContent =
                    'Pembayaran tidak berhasil.';


                Swal.fire({

                    icon: 'error',

                    title: 'Pembayaran Gagal',

                    text:
                        'Pembayaran QRIS tidak berhasil. Silakan coba kembali.',

                    confirmButtonText:
                        'Kembali',

                    confirmButtonColor:
                        '#111827'

                }).then(function () {

                    window.location.href =
                        dashboardUrl;

                });


                return;

            }


        } catch (error) {


            console.error(
                'QRIS status error:',
                error
            );


            checkingText.textContent =
                'Sedang menunggu konfirmasi pembayaran...';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CEK PERTAMA
    |--------------------------------------------------------------------------
    */

    checkPaymentStatus();


    /*
    |--------------------------------------------------------------------------
    | AUTO CHECK
    |--------------------------------------------------------------------------
    |
    | Cek setiap 3 detik.
    |
    */

    checkingInterval =
        setInterval(
            checkPaymentStatus,
            3000
        );


    /*
    |--------------------------------------------------------------------------
    | STOP SAAT USER MENINGGALKAN HALAMAN
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeunload',
        function () {

            checkingPayment =
                false;

            if (checkingInterval) {

                clearInterval(
                    checkingInterval
                );

            }

        }
    );

});

</script>

@endsection
