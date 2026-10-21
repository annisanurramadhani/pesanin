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
            <div class="rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">

                {{-- INFORMASI PEMBAYARAN --}}
                <div class="border-b border-slate-200 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Total Pembayaran
                            </p>

                            <p class="mt-1 text-3xl font-black text-slate-900">
                                Rp {{ number_format($subscription->price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                            <i class="fa-solid fa-wallet text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- COUNTDOWN PEMBAYARAN --}}
                <div class="px-6 pt-5">
                    <div id="payment-countdown-container"
                        data-expires-at="{{ isset($paymentExpiresAt) && $paymentExpiresAt ? \Illuminate\Support\Carbon::parse($paymentExpiresAt)->toIso8601String() : '' }}"
                        class="mx-auto max-w-sm rounded-xl border border-amber-100 bg-amber-50 px-4 py-4 text-center">
                        <p id="payment-countdown-title" class="text-xs font-bold uppercase tracking-wide text-amber-700">
                            Batas Waktu Pembayaran
                        </p>

                        <p id="payment-countdown" class="mt-2 text-2xl font-black tabular-nums text-amber-700">
                            24:00:00
                        </p>

                        <p id="payment-countdown-description" class="mt-1 text-xs text-slate-500">
                            Selesaikan pembayaran sebelum waktu habis.
                        </p>
                    </div>
                </div>

                {{-- QR CODE --}}
                <div class="px-6 py-8">
                    <div class="text-center">
                        <p class="text-sm font-bold text-slate-700">
                            Scan QR Code
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Gunakan GoPay atau aplikasi pembayaran lain
                            yang mendukung QRIS.
                        </p>

                        {{-- QR --}}
                        <div
                            class="mx-auto mt-6 flex w-fit items-center justify-center rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <img src="{{ $qrUrl }}" alt="QRIS Pembayaran" class="h-64 w-64 object-contain">
                        </div>

                        {{-- STATUS --}}
                        <div id="payment-status"
                            class="mx-auto mt-6 flex max-w-sm items-center justify-center gap-2 rounded-xl bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">
                            <i id="payment-status-icon" class="fa-solid fa-clock"></i>

                            <span id="payment-status-text">
                                Menunggu pembayaran
                            </span>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-6 py-7">
                            <div class="mx-auto max-w-md">
                                <div class="text-center">
                                    <h3 class="text-base font-semibold text-slate-900">
                                        Cara Pembayaran
                                    </h3>

                                    <p class="mt-1 text-sm leading-5 text-slate-500">
                                        Ikuti langkah berikut untuk menyelesaikan pembayaran langganan Anda.
                                    </p>
                                </div>

                                <ol class="mt-5 space-y-3">
                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-amber-600 shadow-sm">
                                            1
                                        </span>
                                        <p class="pt-0.5 text-sm leading-5 text-slate-700">
                                            Buka aplikasi pembayaran yang mendukung QRIS.
                                        </p>
                                    </li>

                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-amber-600 shadow-sm">
                                            2
                                        </span>
                                        <p class="pt-0.5 text-sm leading-5 text-slate-700">
                                            Pilih menu Scan QR atau pemindaian QRIS.
                                        </p>
                                    </li>

                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-amber-600 shadow-sm">
                                            3
                                        </span>
                                        <p class="pt-0.5 text-sm leading-5 text-slate-700">
                                            Pindai kode QR yang ditampilkan pada halaman ini.
                                        </p>
                                    </li>

                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-amber-600 shadow-sm">
                                            4
                                        </span>
                                        <p class="pt-0.5 text-sm leading-5 text-slate-700">
                                            Pastikan nominal pembayaran sudah sesuai.
                                        </p>
                                    </li>

                                    <li class="flex items-start gap-3">
                                        <span
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-sm font-semibold text-amber-600 shadow-sm">
                                            5
                                        </span>
                                        <p class="pt-0.5 text-sm leading-5 text-slate-700">
                                            Konfirmasi pembayaran dan tunggu verifikasi otomatis.
                                        </p>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        {{-- ORDER ID --}}
                        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                ID Pesanan
                            </p>

                            <div class="mt-2 flex items-start justify-between gap-3">
                                <p class="break-all text-sm font-bold leading-6 text-slate-800">
                                    {{ $orderId }}
                                </p>
                            </div>
                        </div>

                        {{-- STATUS CHECK INFO --}}
                        <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3">
                            <p id="checking-text" class="flex items-start gap-2 text-left text-xs leading-5 text-amber-800">
                                <i class="fa-solid fa-rotate mt-0.5 shrink-0"></i>
                                <span>Status pembayaran akan diperiksa secara otomatis. Silakan tetap menunggu
                                    konfirmasi.</span>
                            </p>
                        </div>


                        {{-- ================================================================
                            SWEETALERT
                        ================================================================ --}}
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


                        <script>
                            document.addEventListener('DOMContentLoaded', function() {


                                /*
                                |--------------------------------------------------------------------------
                                | DATA
                                |--------------------------------------------------------------------------
                                */

                                const encryptedSubscription =
                                    @json(encryptId($subscription->id));


                                const statusUrl =
                                    @json(route('public.subscription.payment.qris.status', encryptId($subscription->id)));


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


                                const countdownContainer = document.getElementById(
                                    'payment-countdown-container'
                                );

                                const countdownElement = document.getElementById(
                                    'payment-countdown'
                                );

                                const countdownTitle = document.getElementById(
                                    'payment-countdown-title'
                                );

                                const countdownDescription = document.getElementById(
                                    'payment-countdown-description'
                                );

                                const expiryValue = countdownContainer?.dataset.expiresAt || '';

                                let countdownInterval = null;

                                function updatePaymentCountdown() {
                                    if (!countdownElement || !countdownContainer) {
                                        return;
                                    }

                                    if (!expiryValue) {
                                        countdownElement.textContent = '--:--:--';
                                        countdownTitle.textContent = 'Batas Waktu Pembayaran';
                                        countdownDescription.textContent =
                                            'Waktu kedaluwarsa belum tersedia dari Midtrans.';
                                        return;
                                    }

                                    const expiry = new Date(expiryValue).getTime();

                                    if (!Number.isFinite(expiry)) {
                                        countdownElement.textContent = '--:--:--';
                                        countdownDescription.textContent =
                                            'Waktu kedaluwarsa tidak dapat dibaca.';
                                        return;
                                    }

                                    const remaining = expiry - Date.now();

                                    if (remaining <= 0) {
                                        countdownElement.textContent = '00:00:00';

                                        countdownContainer.classList.remove(
                                            'bg-amber-50',
                                            'border-amber-100'
                                        );
                                        countdownContainer.classList.add(
                                            'bg-red-50',
                                            'border-red-100'
                                        );

                                        countdownElement.classList.remove('text-amber-700');
                                        countdownElement.classList.add('text-red-700');

                                        countdownTitle.textContent = 'Waktu Pembayaran Habis';
                                        countdownDescription.textContent =
                                            'Batas waktu transaksi telah tercapai.';

                                        if (countdownInterval) {
                                            clearInterval(countdownInterval);
                                            countdownInterval = null;
                                        }

                                        return;
                                    }

                                    const totalSeconds = Math.floor(remaining / 1000);
                                    const hours = Math.floor(totalSeconds / 3600);
                                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                                    const seconds = totalSeconds % 60;

                                    countdownElement.textContent = [
                                        String(hours).padStart(2, '0'),
                                        String(minutes).padStart(2, '0'),
                                        String(seconds).padStart(2, '0'),
                                    ].join(':');
                                }

                                updatePaymentCountdown();
                                countdownInterval = setInterval(updatePaymentCountdown, 1000);

                                window.addEventListener('beforeunload', function() {
                                    if (countdownInterval) {
                                        clearInterval(countdownInterval);
                                    }
                                });


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
                                                statusUrl, {

                                                    method: 'GET',

                                                    headers: {

                                                        'Accept': 'application/json',

                                                        'X-Requested-With': 'XMLHttpRequest',

                                                    },

                                                    credentials: 'same-origin',

                                                    cache: 'no-store',

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
                                            data.paid === true &&
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

                                                text: 'Langganan Anda berhasil diperpanjang dan sudah aktif.',

                                                confirmButtonText: 'Lanjutkan',

                                                confirmButtonColor: '#f59e0b',

                                                allowOutsideClick: false,

                                                allowEscapeKey: false,

                                            }).then(function() {

                                                /*
                                                |--------------------------------------------------------------------------
                                                | Dashboard
                                                |--------------------------------------------------------------------------
                                                */

                                                window.location.href =
                                                    data.redirect ||
                                                    dashboardUrl;

                                            });


                                            return;

                                        }


                                        /*
                                        |--------------------------------------------------------------------------
                                        | PENDING
                                        |--------------------------------------------------------------------------
                                        */

                                        if (
                                            data.status === 'pending' ||
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

                                                text: 'QRIS ini sudah kedaluwarsa. Silakan kembali dan buat pembayaran baru.',

                                                confirmButtonText: 'Kembali',

                                                confirmButtonColor: '#f59e0b'

                                            }).then(function() {

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
                                            data.status === 'cancel' ||
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

                                                text: 'Pembayaran QRIS tidak berhasil. Silakan coba kembali.',

                                                confirmButtonText: 'Kembali',

                                                confirmButtonColor: '#111827'

                                            }).then(function() {

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
                                    function() {

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
