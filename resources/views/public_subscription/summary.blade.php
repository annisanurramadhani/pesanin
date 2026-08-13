@extends('layouts.app')

@section('body')

    <div class="min-h-screen bg-slate-50">

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

                <a href="{{ route('login') }}"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    Masuk
                </a>

            </div>
        </header>


        <section class="px-6 pb-20 pt-14">

            <div class="mx-auto max-w-5xl">

                <a href="{{ route('public.subscription.show', $package->slug) }}"
                    class="mb-8 inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-slate-900">

                    <i class="fa-solid fa-arrow-left"></i>

                    Kembali ke Pilihan Durasi

                </a>


                <div class="mb-10">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">
                        Ringkasan Pesanan
                    </span>

                    <h2 class="mt-4 text-4xl font-black tracking-tight text-slate-900">
                        Konfirmasi Pesanan
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                        Periksa kembali paket dan durasi langganan Anda sebelum melanjutkan.
                    </p>

                </div>


                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


                    <div class="lg:col-span-2">

                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-200 px-6 py-5">

                                <h3 class="text-lg font-extrabold text-slate-900">
                                    Detail Pesanan
                                </h3>

                            </div>


                            <div class="space-y-6 p-6">

                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                        <i class="fa-solid fa-box"></i>

                                    </div>

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Paket
                                        </p>

                                        <p class="mt-1 text-lg font-extrabold text-slate-900">
                                            {{ $package->name }}
                                        </p>

                                    </div>

                                </div>


                                <div class="border-t border-slate-100"></div>


                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-500">

                                        <i class="fa-solid fa-calendar-days"></i>

                                    </div>

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Durasi
                                        </p>

                                        <p class="mt-1 text-lg font-extrabold text-slate-900">
                                            {{ $duration->name }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $duration->duration_days }} hari
                                        </p>

                                    </div>

                                </div>


                                @if ($package->description)

                                    <div class="border-t border-slate-100 pt-6">

                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Deskripsi Paket
                                        </p>

                                        <p class="mt-2 text-sm leading-6 text-slate-600">
                                            {{ $package->description }}
                                        </p>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>


                    <div>

                        <div class="sticky top-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-200 px-6 py-5">

                                <h3 class="text-lg font-extrabold text-slate-900">
                                    Total Pembayaran
                                </h3>

                            </div>


                            <div class="p-6">

                                @if ($hasDiscount)

                                    <div class="flex items-center justify-between">

                                        <span class="text-sm text-slate-500">
                                            Harga Normal
                                        </span>

                                        <span class="text-sm font-semibold text-slate-400 line-through">
                                            Rp {{ number_format($duration->price, 0, ',', '.') }}
                                        </span>

                                    </div>

                                @endif


                                <div class="mt-3 flex items-center justify-between gap-4">

                                    <span class="text-sm text-slate-500">
                                        {{ $duration->name }}
                                    </span>

                                    <span class="text-sm font-bold text-slate-900">
                                        Rp {{ number_format($price, 0, ',', '.') }}
                                    </span>

                                </div>


                                <div class="my-5 border-t border-slate-200"></div>


                                <div class="flex items-end justify-between gap-4">

                                    <span class="text-sm font-bold text-slate-600">
                                        Total
                                    </span>

                                    <span class="text-2xl font-black text-slate-900">
                                        Rp {{ number_format($price, 0, ',', '.') }}
                                    </span>

                                </div>


                                <div class="mt-6 rounded-xl bg-slate-50 p-4">

                                    <div class="flex gap-3">

                                        <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>

                                        <p class="text-xs leading-5 text-slate-500">
                                            Langganan akan diproses setelah pembayaran berhasil.
                                        </p>

                                    </div>

                                </div>


                                <div class="mt-6">

                                    <a href="{{ route('login') }}"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                        <span>
                                            Lanjutkan
                                        </span>

                                        <i class="fa-solid fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>

@endsection