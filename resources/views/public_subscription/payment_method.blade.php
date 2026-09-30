@extends('layouts.app')

@section('body')

    <div class="min-h-screen bg-slate-50">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <header class="border-b border-slate-200 bg-white">

            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">

                {{-- Brand --}}
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-950">
                        PesanYuk
                    </h1>

                    <p class="mt-0.5 text-xs text-slate-500">
                        Solusi digital untuk bisnis Anda
                    </p>
                </div>


                {{-- Secure Payment --}}
                <div
                    class="flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-4 py-2">

                    <div
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">

                        <i class="fa-solid fa-lock text-[10px]"></i>

                    </div>

                    <span class="hidden text-xs font-bold text-slate-600 sm:inline">
                        Pembayaran Aman
                    </span>

                </div>

            </div>

        </header>


        {{-- =========================================================
            MAIN CONTENT
        ========================================================== --}}
        <main class="px-5 py-12 sm:px-6 lg:py-16">

            <div class="mx-auto max-w-6xl">


                {{-- =================================================
                    PAGE HEADING
                ================================================== --}}
                <div class="mb-10 text-center">

                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-[11px] font-extrabold uppercase tracking-[0.16em] text-amber-600">

                        <i class="fa-solid fa-building-columns text-[10px]"></i>

                        Transfer Bank

                    </span>


                    <h2
                        class="mt-5 text-3xl font-black tracking-tight text-slate-950 sm:text-5xl">

                        Pilih Bank

                    </h2>


                    <p
                        class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">

                        Pilih bank yang ingin Anda gunakan untuk melakukan pembayaran
                        melalui Virtual Account.

                    </p>

                </div>


                {{-- =================================================
                    PAYMENT CONTAINER
                ================================================== --}}
                <div
                    class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_20px_60px_rgba(15,23,42,0.08)]">


                    {{-- =================================================
                        PAYMENT SUMMARY
                    ================================================== --}}
                    <div
                        class="border-b border-slate-200 px-6 py-6 sm:px-8 lg:px-10">

                        <div
                            class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">


                            {{-- Total --}}
                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-[0.14em] text-slate-400">

                                    Total Pembayaran

                                </p>


                                <p
                                    class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">

                                    Rp
                                    {{ number_format($subscription->price, 0, ',', '.') }}

                                </p>

                            </div>


                            {{-- Secure --}}
                            <div
                                class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm">

                                    <i class="fa-solid fa-shield-halved"></i>

                                </div>


                                <div>

                                    <p
                                        class="text-xs font-extrabold text-slate-700">

                                        Pembayaran Aman

                                    </p>

                                    <p
                                        class="mt-0.5 text-[11px] text-slate-400">

                                        Diproses secara aman melalui Midtrans

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        SUBSCRIPTION SUMMARY
                    ================================================== --}}
                    <div
                        class="border-b border-slate-200 px-6 py-6 sm:px-8 lg:px-10">

                        <div
                            class="grid grid-cols-1 gap-4 sm:grid-cols-3">


                            {{-- Package --}}
                            <div
                                class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                        <i class="fa-solid fa-box"></i>

                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400">

                                            Paket

                                        </p>

                                        <p
                                            class="mt-1 truncate text-sm font-extrabold text-slate-900">

                                            {{ $subscription->packageDuration->package->name }}

                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- Duration --}}
                            <div
                                class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500">

                                        <i class="fa-solid fa-calendar-days"></i>

                                    </div>


                                    <div>

                                        <p
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400">

                                            Durasi

                                        </p>

                                        <p
                                            class="mt-1 text-sm font-extrabold text-slate-900">

                                            {{ $subscription->packageDuration->name }}

                                        </p>

                                    </div>

                                </div>

                            </div>


                            {{-- Merchant --}}
                            <div
                                class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">

                                        <i class="fa-solid fa-store"></i>

                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400">

                                            Toko

                                        </p>

                                        <p
                                            class="mt-1 truncate text-sm font-extrabold text-slate-900">

                                            {{ $subscription->merchant->name }}

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        BANK SELECTION
                    ================================================== --}}
                    <div
                        class="px-6 py-8 sm:px-8 lg:px-10 lg:py-10">


                        {{-- Heading --}}
                        <div class="mb-7">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                    <i class="fa-solid fa-building-columns"></i>

                                </div>


                                <div>

                                    <h3
                                        class="text-xl font-black tracking-tight text-slate-950">

                                        Pilih Bank Anda

                                    </h3>

                                    <p
                                        class="mt-1 text-sm text-slate-500">

                                        Pilih salah satu bank untuk mendapatkan Virtual Account.

                                    </p>

                                </div>

                            </div>

                        </div>


                        @php
                            $banks = [
                                'bca' => [
                                    'name' => 'BCA',
                                    'description' => 'Virtual Account BCA',
                                ],

                                'bni' => [
                                    'name' => 'BNI',
                                    'description' => 'Virtual Account BNI',
                                ],

                                'bri' => [
                                    'name' => 'BRI',
                                    'description' => 'Virtual Account BRI',
                                ],

                                'mandiri' => [
                                    'name' => 'Mandiri',
                                    'description' => 'Virtual Account Mandiri',
                                ],

                                'permata' => [
                                    'name' => 'Permata',
                                    'description' => 'Virtual Account Permata',
                                ],

                                'cimb' => [
                                    'name' => 'CIMB Niaga',
                                    'description' => 'Virtual Account CIMB Niaga',
                                ],
                            ];
                        @endphp


                        {{-- =================================================
                            BANK GRID
                        ================================================== --}}
                        <div
                            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">


                            @foreach ($banks as $value => $bank)

                                <form
                                    action="{{ route(
                                        'public.subscription.payment.bank.create',
                                        encryptId($subscription->id)
                                    ) }}"
                                    method="POST"
                                    class="group">

                                    @csrf

                                    <input
                                        type="hidden"
                                        name="bank"
                                        value="{{ $value }}"
                                    >


                                    <button
                                        type="submit"
                                        class="relative flex min-h-[150px] w-full flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 text-left transition-all duration-200 hover:-translate-y-1 hover:border-amber-300 hover:shadow-[0_15px_35px_rgba(15,23,42,0.08)]">


                                        {{-- Top accent --}}
                                        <div
                                            class="absolute inset-x-0 top-0 h-1 bg-amber-500 opacity-0 transition group-hover:opacity-100">
                                        </div>


                                        {{-- Icon + Arrow --}}
                                        <div
                                            class="flex items-center justify-between">

                                            <div
                                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition group-hover:bg-amber-50 group-hover:text-amber-600">

                                                <i
                                                    class="fa-solid fa-building-columns">
                                                </i>

                                            </div>


                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition group-hover:bg-amber-50 group-hover:text-amber-500">

                                                <i
                                                    class="fa-solid fa-arrow-right text-xs">
                                                </i>

                                            </div>

                                        </div>


                                        {{-- Bank information --}}
                                        <div class="mt-5">

                                            <h4
                                                class="text-lg font-black text-slate-950">

                                                {{ $bank['name'] }}

                                            </h4>


                                            <p
                                                class="mt-1 text-xs text-slate-500">

                                                {{ $bank['description'] }}

                                            </p>

                                        </div>


                                        {{-- Footer --}}
                                        <div
                                            class="mt-5 flex items-center gap-2 text-[11px] font-bold text-amber-600">

                                            <i
                                                class="fa-solid fa-circle-check">
                                            </i>

                                            Pilih Bank

                                        </div>

                                    </button>

                                </form>

                            @endforeach

                        </div>


                        {{-- =================================================
                            INFORMATION
                        ================================================== --}}
                        <div
                            class="mt-8 flex gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">

                                <i class="fa-solid fa-circle-info text-sm"></i>

                            </div>


                            <div>

                                <p
                                    class="text-sm font-bold text-slate-700">

                                    Cara Pembayaran

                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-slate-500 sm:text-sm">

                                    Pilih bank yang Anda gunakan. Setelah dipilih,
                                    sistem akan membuat nomor Virtual Account secara otomatis.

                                </p>

                            </div>

                        </div>


                        {{-- =================================================
                            BACK
                        ================================================== --}}
                        <div class="mt-8">

                            <a
                                href="{{ url()->previous() }}"
                                class="inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-bold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">

                                <i class="fa-solid fa-arrow-left text-xs"></i>

                                Kembali

                            </a>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    SECURITY FOOTER
                ================================================== --}}
                <div
                    class="mt-6 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs text-slate-400">

                    <span class="inline-flex items-center gap-2">

                        <i class="fa-solid fa-lock"></i>

                        Transaksi terenkripsi

                    </span>


                    <span
                        class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block">
                    </span>


                    <span class="inline-flex items-center gap-2">

                        <i class="fa-solid fa-shield-halved"></i>

                        Pembayaran aman

                    </span>


                    <span
                        class="hidden h-1 w-1 rounded-full bg-slate-300 sm:block">
                    </span>


                    <span class="inline-flex items-center gap-2">

                        <i class="fa-solid fa-building-columns"></i>

                        Virtual Account

                    </span>

                </div>

            </div>

        </main>

    </div>

@endsection
