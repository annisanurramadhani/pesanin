@extends('layouts.app')

@section('body')

<div class="min-h-screen overflow-hidden bg-white text-slate-900">

    {{-- ============================================================
        HERO SECTION
    ============================================================= --}}
    <section class="relative overflow-hidden">

        {{-- Background Decoration --}}
        <div class="pointer-events-none absolute -right-32 -top-32 h-[500px] w-[500px] rounded-full bg-amber-100/60 blur-3xl"></div>

        <div class="pointer-events-none absolute -left-40 top-80 h-[350px] w-[350px] rounded-full bg-orange-50 blur-3xl"></div>


        <div class="relative mx-auto max-w-7xl px-6 pb-20 pt-12 lg:px-8 lg:pb-28 lg:pt-20">

            <div class="mx-auto max-w-4xl text-center">


                {{-- =================================================
                    LEFT CONTENT
                ================================================== --}}
                

                    {{-- Badge --}}
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold text-amber-600">

                        <i class="fa-solid fa-sparkles"></i>

                        Solusi Digital untuk Bisnis Kuliner

                    </div>


                    {{-- Heading --}}
                    <h1 class="mx-auto max-w-2xl text-4xl font-black leading-[1.08] tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">

                        Kelola Bisnis Kuliner

                        <br>

                        Anda dengan

                        <span class="text-amber-500">
                            Lebih Mudah & Efisien
                        </span>

                    </h1>


                    {{-- Description --}}
                    <p class="mx-auto mt-6 max-w-xl text-base leading-7 text-slate-500 sm:text-lg">

                        PesanIn membantu Anda mengelola menu, pesanan,
                        QR Code, hingga laporan penjualan dalam satu
                        platform yang praktis dan terintegrasi.

                    </p>


                    {{-- CTA --}}
                    <div class="mt-8 flex justify-center gap-3">

                        <a
                            href="{{ route('public.subscription.index') }}"
                            class="inline-flex items-center justify-center gap-3 rounded-xl bg-amber-500 px-7 py-4 text-sm font-extrabold text-slate-950 shadow-xl shadow-amber-500/20 transition hover:-translate-y-0.5 hover:bg-amber-400"
                        >

                            Lihat Paket

                            <i class="fa-solid fa-arrow-right text-xs"></i>

                        </a>

                    </div>


                    {{-- Trust Points --}}
                    <div class="mt-8 flex flex-wrap justify-center gap-3">

                        <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">

                            <i class="fa-solid fa-circle-check text-amber-500"></i>

                            Mudah digunakan

                        </div>


                        <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">

                            <i class="fa-solid fa-shield-halved text-amber-500"></i>

                            Aman & Terpercaya

                        </div>


                        <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 shadow-sm">

                            <i class="fa-solid fa-bolt text-amber-500"></i>

                            Hemat Waktu

                        </div>

                    </div>


                

            </div>

        </div>

    </section>


    {{-- ============================================================
        BENEFITS
    ============================================================= --}}
    <section class="border-y border-slate-100 bg-slate-50/70">

        <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">

            <div class="mb-10 text-center">

                <p class="text-sm font-extrabold uppercase tracking-widest text-amber-500">
                    Kenapa PesanIn?
                </p>

                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">
                    Semua yang Anda Butuhkan
                    <span class="text-amber-500">
                        dalam Satu Platform
                    </span>
                </h2>

            </div>


            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">


                {{-- Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">

                        <i class="fa-solid fa-book-open text-xl"></i>

                    </div>

                    <h3 class="font-extrabold text-slate-900">
                        Kelola Menu
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Tambah, ubah, dan kelola menu bisnis Anda dengan mudah.
                    </p>

                </div>


                {{-- Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">

                        <i class="fa-solid fa-bag-shopping text-xl"></i>

                    </div>

                    <h3 class="font-extrabold text-slate-900">
                        Terima Pesanan
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Kelola semua pesanan pelanggan dengan lebih cepat dan terorganisir.
                    </p>

                </div>


                {{-- Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">

                        <i class="fa-solid fa-qrcode text-xl"></i>

                    </div>

                    <h3 class="font-extrabold text-slate-900">
                        QR Code Menu
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Tampilkan menu digital melalui QR Code di meja pelanggan.
                    </p>

                </div>


                {{-- Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">

                        <i class="fa-solid fa-chart-column text-xl"></i>

                    </div>

                    <h3 class="font-extrabold text-slate-900">
                        Laporan Penjualan
                    </h3>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Pantau penjualan dan performa bisnis secara lebih mudah.
                    </p>

                </div>


            </div>

        </div>

    </section>


    {{-- ============================================================
        HOW IT WORKS
    ============================================================= --}}
    <section class="bg-white">

        <div class="mx-auto max-w-6xl px-6 py-20 lg:px-8">

            <div class="mx-auto max-w-2xl text-center">

                <p class="text-sm font-extrabold uppercase tracking-widest text-amber-500">
                    Cara Kerja
                </p>

                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                    Mulai Kelola Bisnis dalam
                    <span class="text-amber-500">
                        3 Langkah
                    </span>
                </h2>

            </div>


            <div class="relative mt-14 grid gap-10 md:grid-cols-3">

                {{-- Step --}}
                <div class="relative text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500 text-xl font-black text-white shadow-lg shadow-amber-500/20">
                        01
                    </div>

                    <h3 class="mt-5 font-extrabold text-slate-900">
                        Buat Akun
                    </h3>

                    <p class="mx-auto mt-2 max-w-xs text-sm leading-6 text-slate-500">
                        Daftarkan bisnis Anda dan mulai menggunakan PesanIn.
                    </p>

                </div>


                {{-- Step --}}
                <div class="relative text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500 text-xl font-black text-white shadow-lg shadow-amber-500/20">
                        02
                    </div>

                    <h3 class="mt-5 font-extrabold text-slate-900">
                        Atur Bisnis
                    </h3>

                    <p class="mx-auto mt-2 max-w-xs text-sm leading-6 text-slate-500">
                        Tambahkan menu, QR Code, meja, dan informasi bisnis Anda.
                    </p>

                </div>


                {{-- Step --}}
                <div class="relative text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-500 text-xl font-black text-white shadow-lg shadow-amber-500/20">
                        03
                    </div>

                    <h3 class="mt-5 font-extrabold text-slate-900">
                        Mulai Berjualan
                    </h3>

                    <p class="mx-auto mt-2 max-w-xs text-sm leading-6 text-slate-500">
                        Terima pesanan dan pantau perkembangan bisnis Anda.
                    </p>

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================================
        CTA
    ============================================================= --}}
    <section class="px-6 pb-20">

        <div class="relative mx-auto max-w-6xl overflow-hidden rounded-[2rem] bg-[#111827] px-6 py-16 text-center shadow-2xl sm:px-10">

            {{-- Decoration --}}
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-amber-500/20 blur-3xl"></div>

            <div class="pointer-events-none absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-amber-500/10 blur-3xl"></div>


            <div class="relative">

                <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-lg">

                    <img
                        src="{{ asset('assets/images/logo-regis.jpg') }}"
                        alt="PesanIn"
                        class="h-full w-full object-cover"
                    >

                </div>


                <h2 class="text-3xl font-black tracking-tight text-white sm:text-4xl">
                    Siap Membuat Bisnis Anda
                    <span class="text-amber-400">
                        Lebih Mudah?
                    </span>
                </h2>


                <p class="mx-auto mt-4 max-w-xl text-sm leading-6 text-slate-400">
                    Bergabung dengan PesanIn dan nikmati cara yang lebih praktis
                    untuk mengelola bisnis kuliner Anda.
                </p>


                <div class="mt-8">

                    <a
                        href="{{ route('public.subscription.index') }}"
                        class="inline-flex items-center gap-3 rounded-xl bg-amber-500 px-7 py-4 text-sm font-extrabold text-slate-950 shadow-xl shadow-amber-500/20 transition hover:bg-amber-400"
                    >

                        Mulai Sekarang

                        <i class="fa-solid fa-arrow-right text-xs"></i>

                    </a>

                </div>

            </div>

        </div>

    </section>


    {{-- ============================================================
        SIMPLE FOOTER
    ============================================================= --}}
    <footer class="border-t border-slate-100 bg-white">

        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-7 text-center sm:flex-row sm:text-left lg:px-8">

            <div class="flex items-center gap-2">

                <div class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-lg">

                    <img
                        src="{{ asset('assets/images/logo-regis.jpg') }}"
                        alt="PesanIn"
                        class="h-full w-full object-cover"
                    >

                </div>

                <span class="text-sm font-extrabold text-slate-900">
                    PesanIn
                </span>

            </div>


            <p class="text-xs text-slate-400">
                © {{ date('Y') }} PesanIn. Semua hak dilindungi.
            </p>

        </div>

    </footer>

</div>

@endsection