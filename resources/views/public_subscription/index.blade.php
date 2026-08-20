@extends('layouts.app')

@section('body')

    <div class="min-h-screen bg-slate-50">

        {{-- Header --}}
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


        {{-- Hero --}}
        <section class="px-6 pb-12 pt-16 text-center">

            <span
                class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">
                Paket Langganan
            </span>

            <h2 class="mx-auto mt-5 max-w-3xl text-4xl font-black tracking-tight text-slate-900">
                Pilih Paket yang Sesuai
                <span class="text-amber-500">
                    untuk Bisnis Anda
                </span>
            </h2>

            <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-slate-500">
                Gunakan PesanIn untuk mengelola menu, pesanan, QR Code,
                dan operasional toko dengan lebih mudah.
            </p>

        </section>


        {{-- Package List --}}
        <section class="mx-auto max-w-7xl px-6 pb-20">

            @if ($packages->isEmpty())

                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">

                    <div
                        class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <i class="fa-solid fa-box-open text-xl"></i>
                    </div>

                    <h3 class="mt-4 font-extrabold text-slate-800">
                        Paket belum tersedia
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Saat ini belum terdapat paket langganan yang tersedia.
                    </p>

                </div>

            @else

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                    @foreach ($packages as $package)

                        <div
                            class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                            {{-- Card --}}
                            <div class="flex flex-1 flex-col p-6">

                                {{-- Nama Paket --}}
                                <div class="flex items-start justify-between gap-4">

                                    <div>

                                        <h3 class="text-2xl font-black text-slate-900">
                                            {{ $package->name }}
                                        </h3>

                                        @if ($package->badge)

                                            <span
                                                class="mt-2 inline-block rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-600">
                                                {{ $package->badge }}
                                            </span>

                                        @endif

                                    </div>

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                                        <i class="fa-solid fa-crown"></i>
                                    </div>

                                </div>


                                {{-- Deskripsi --}}
                                @if ($package->description)

                                    <div class="mt-5 text-sm leading-6 text-slate-500">
                                        {!! $package->description !!}
                                    </div>

                                @endif


                                {{-- Info --}}
                                <div class="mt-6 flex items-center gap-3 text-sm text-slate-600">

                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>

                                    <span>
                                        Tersedia beberapa pilihan durasi
                                    </span>

                                </div>


                                {{-- Button --}}
                                <div class="mt-auto pt-8">

                                    <a href="{{ route('public.subscription.show', $package->slug) }}"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                        <span>
                                            Lihat Durasi
                                        </span>

                                        <i class="fa-solid fa-arrow-right"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </section>

        {{-- ======================================================
                FOOTER
            ======================================================= --}}
            <footer class="border-t border-slate-200 pt-6 pb-8 text-center">

                <p class="text-[11px] text-slate-400">
                    Powered by
                    <span class="font-extrabold text-slate-500">
                        PesanIn
                    </span>
                </p>
            </footer>

    </div>
@endsection
