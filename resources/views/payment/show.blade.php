@extends('layouts.app')

@section('body')

    <div class="min-h-screen bg-slate-50">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <header class="border-b border-slate-200 bg-white">

            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">

                <div>

                    <h1 class="text-xl font-extrabold text-slate-900">
                        PesanIn
                    </h1>

                    <p class="text-xs text-slate-500">
                        Solusi digital untuk bisnis Anda
                    </p>

                </div>

                <span
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-500">

                    Pembayaran

                </span>

            </div>

        </header>


        {{-- =========================================================
            CONTENT
        ========================================================== --}}
        <main class="px-6 py-14">

            <div class="mx-auto max-w-4xl">


                {{-- =================================================
                    HEADING
                ================================================== --}}
                <div class="mb-10 text-center">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">

                        Pembayaran Langganan

                    </span>

                    <h2 class="mt-5 text-4xl font-black tracking-tight text-slate-900">

                        Selesaikan Pembayaran

                    </h2>

                    <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500">

                        Silakan pilih metode pembayaran untuk mengaktifkan
                        langganan PesanIn Anda.

                    </p>

                </div>


                {{-- =================================================
                    PAYMENT CARD
                ================================================== --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">


                    {{-- =================================================
                        DETAIL LANGGANAN
                    ================================================== --}}
                    <div class="md:col-span-2">

                        <div
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                            {{-- Card Header --}}
                            <div class="border-b border-slate-200 px-6 py-5">

                                <h3 class="text-lg font-extrabold text-slate-900">

                                    Detail Langganan

                                </h3>

                            </div>


                            {{-- Card Body --}}
                            <div class="space-y-6 p-6">


                                {{-- Paket --}}
                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                        <i class="fa-solid fa-box"></i>

                                    </div>


                                    <div>

                                        <p
                                            class="text-xs font-semibold uppercase tracking-wide text-slate-400">

                                            Paket

                                        </p>

                                        <p
                                            class="mt-1 text-lg font-extrabold text-slate-900">

                                            {{ $subscription->packageDuration->package->name }}

                                        </p>

                                    </div>

                                </div>


                                <div class="border-t border-slate-100"></div>


                                {{-- Durasi --}}
                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500">

                                        <i class="fa-solid fa-calendar-days"></i>

                                    </div>


                                    <div>

                                        <p
                                            class="text-xs font-semibold uppercase tracking-wide text-slate-400">

                                            Durasi

                                        </p>

                                        <p
                                            class="mt-1 text-lg font-extrabold text-slate-900">

                                            {{ $subscription->packageDuration->name }}

                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">

                                            {{ $subscription->packageDuration->duration_days }}
                                            hari

                                        </p>

                                    </div>

                                </div>


                                <div class="border-t border-slate-100"></div>


                                {{-- Merchant --}}
                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">

                                        <i class="fa-solid fa-store"></i>

                                    </div>


                                    <div>

                                        <p
                                            class="text-xs font-semibold uppercase tracking-wide text-slate-400">

                                            Toko

                                        </p>

                                        <p
                                            class="mt-1 text-lg font-extrabold text-slate-900">

                                            {{ $subscription->merchant->name }}

                                        </p>

                                    </div>

                                </div>


                                <div class="border-t border-slate-100"></div>


                                {{-- Status --}}
                                <div class="flex items-center justify-between">

                                    <span class="text-sm text-slate-500">

                                        Status Pembayaran

                                    </span>


                                    <span
                                        class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">

                                        Menunggu Pembayaran

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        PAYMENT
                    ================================================== --}}
                    <div>

                        <div
                            class="sticky top-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                            {{-- Card Header --}}
                            <div class="border-b border-slate-200 px-6 py-5">

                                <h3 class="text-lg font-extrabold text-slate-900">

                                    Total Pembayaran

                                </h3>

                            </div>


                            {{-- Card Body --}}
                            <div class="p-6">


                                {{-- Total --}}
                                <div class="flex items-center justify-between gap-4">

                                    <span class="text-sm text-slate-500">

                                        Total

                                    </span>


                                    <span class="text-xl font-black text-slate-900">

                                        Rp
                                        {{ number_format($subscription->price, 0, ',', '.') }}

                                    </span>

                                </div>


                                <div class="my-5 border-t border-slate-200"></div>


                                {{-- Info --}}
                                <div class="rounded-xl bg-slate-50 p-4">

                                    <div class="flex gap-3">

                                        <i
                                            class="fa-solid fa-circle-info mt-0.5 text-amber-500">
                                        </i>


                                        <p class="text-xs leading-5 text-slate-500">

                                            Pilih metode pembayaran yang ingin
                                            Anda gunakan.

                                        </p>

                                    </div>

                                </div>


                                {{-- =================================================
                                    SNAP DEFAULT
                                ================================================== --}}
                                <button
                                    type="button"
                                    id="pay-button"
                                    class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                    <i class="fa-solid fa-credit-card"></i>

                                    <span>
                                        Bayar Sekarang
                                    </span>

                                </button>


                                {{-- =================================================
                                    QRIS CUSTOM
                                ================================================== --}}
                                <a
                                    href="{{ route(
                                        'public.subscription.payment.qris',
                                        encryptId($subscription->id)
                                    ) }}"
                                    id="qris-button"
                                    class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-5 py-3.5 text-sm font-extrabold text-slate-800 transition hover:border-amber-400 hover:bg-amber-50">

                                    <i class="fa-solid fa-qrcode text-amber-500"></i>

                                    <span>
                                        Bayar via QRIS
                                    </span>

                                </a>


                                {{-- Security --}}
                                <div class="mt-4 text-center">

                                    <p class="text-xs text-slate-400">

                                        Pembayaran diproses secara aman
                                        melalui Midtrans.

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </main>

    </div>


    {{-- =============================================================
        MIDTRANS SNAP JS
    ============================================================= --}}
    <script
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
    </script>


    {{-- =============================================================
        MIDTRANS SNAP SCRIPT
    ============================================================= --}}
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const payButton =
                document.getElementById('pay-button');

            const qrisButton =
                document.getElementById('qris-button');


            if (!payButton) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | BAYAR MENGGUNAKAN SNAP
            |--------------------------------------------------------------------------
            */

            payButton.addEventListener('click', function () {

                /*
                |--------------------------------------------------------------------------
                | Cek Snap
                |--------------------------------------------------------------------------
                */

                if (typeof snap === 'undefined') {

                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Tidak Tersedia',
                        text: 'Midtrans belum berhasil dimuat. Silakan refresh halaman.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#f59e0b'
                    });

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Disable Button
                |--------------------------------------------------------------------------
                */

                payButton.disabled = true;

                payButton.classList.add(
                    'cursor-not-allowed',
                    'opacity-70'
                );


                payButton.innerHTML = `
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <span>Memuat Pembayaran...</span>
                `;


                /*
                |--------------------------------------------------------------------------
                | Buka Snap
                |--------------------------------------------------------------------------
                */

                snap.pay(

                    @json($snapToken),

                    {

                        /*
                        |--------------------------------------------------------------------------
                        | SUCCESS
                        |--------------------------------------------------------------------------
                        */

                        onSuccess: function (result) {

                            console.log(
                                'Midtrans Success:',
                                result
                            );


                            Swal.fire({

                                icon: 'success',

                                title: 'Pembayaran Berhasil!',

                                text:
                                    'Pembayaran Anda berhasil diproses. Langganan sedang diaktifkan.',

                                confirmButtonText:
                                    'Lanjutkan',

                                confirmButtonColor:
                                    '#f59e0b',

                                allowOutsideClick:
                                    false

                            }).then(function () {

                                window.location.href =
                                    "{{ route('dashboard') }}";

                            });

                        },


                        /*
                        |--------------------------------------------------------------------------
                        | PENDING
                        |--------------------------------------------------------------------------
                        */

                        onPending: function (result) {

                            console.log(
                                'Midtrans Pending:',
                                result
                            );


                            Swal.fire({

                                icon: 'info',

                                title: 'Pembayaran Menunggu',

                                text:
                                    'Pembayaran belum selesai. Silakan selesaikan pembayaran Anda.',

                                confirmButtonText:
                                    'OK',

                                confirmButtonColor:
                                    '#f59e0b'

                            });


                            resetPayButton();

                        },


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR
                        |--------------------------------------------------------------------------
                        */

                        onError: function (result) {

                            console.error(
                                'Midtrans Error:',
                                result
                            );


                            Swal.fire({

                                icon: 'error',

                                title: 'Pembayaran Gagal',

                                text:
                                    'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',

                                confirmButtonText:
                                    'Coba Lagi',

                                confirmButtonColor:
                                    '#111827'

                            });


                            resetPayButton();

                        },


                        /*
                        |--------------------------------------------------------------------------
                        | CLOSE
                        |--------------------------------------------------------------------------
                        */

                        onClose: function () {

                            console.log(
                                'Popup Midtrans ditutup.'
                            );


                            resetPayButton();

                        }

                    }

                );

            });


            /*
            |--------------------------------------------------------------------------
            | RESET BUTTON
            |--------------------------------------------------------------------------
            */

            function resetPayButton() {

                payButton.disabled = false;

                payButton.classList.remove(
                    'cursor-not-allowed',
                    'opacity-70'
                );

                payButton.innerHTML = `
                    <i class="fa-solid fa-credit-card"></i>
                    <span>Bayar Sekarang</span>
                `;

            }

        });

    </script>

@endsection
