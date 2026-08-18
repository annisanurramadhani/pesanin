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


        {{-- Content --}}
        <section class="px-6 pb-20 pt-14">

            <div class="mx-auto max-w-7xl">

                {{-- Back --}}
                <a href="{{ route('public.subscription.index') }}"
                    class="mb-8 inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-slate-900">

                    <i class="fa-solid fa-arrow-left"></i>

                    Kembali ke Pilihan Paket

                </a>


                {{-- Title --}}
                <div class="mb-10">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">
                        {{ $package->name }}
                    </span>

                    <h2 class="mt-4 text-4xl font-black tracking-tight text-slate-900">
                        Pilih Durasi Langganan
                    </h2>

                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                        Pilih durasi langganan yang sesuai dengan kebutuhan bisnis Anda.
                    </p>

                </div>


                {{-- Durations --}}
                @if ($durations->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center shadow-sm">

                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fa-solid fa-calendar-xmark text-xl"></i>
                        </div>

                        <h3 class="mt-4 font-extrabold text-slate-800">
                            Durasi belum tersedia
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Belum ada pilihan durasi untuk paket ini.
                        </p>

                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                        @foreach ($durations as $duration)
                            @php
                                $price = $duration->discount_price ?? $duration->price;
                                $hasDiscount = !is_null($duration->discount_price);
                            @endphp

                            <div
                                class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                                <div class="flex flex-1 flex-col p-6">

                                    {{-- Duration --}}
                                    <div class="flex items-start justify-between gap-4">

                                        <div>

                                            <h3 class="text-2xl font-black text-slate-900">
                                                {{ $duration->name }}
                                            </h3>

                                            <p class="mt-1 text-sm font-semibold text-slate-400">
                                                {{ $duration->duration_days }} hari
                                            </p>

                                        </div>

                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                            <i class="fa-solid fa-calendar-days"></i>

                                        </div>

                                    </div>


                                    {{-- Price --}}
                                    <div class="mt-7">

                                        @if ($hasDiscount)
                                            <p class="text-sm font-semibold text-slate-400 line-through">
                                                Rp {{ number_format($duration->price, 0, ',', '.') }}
                                            </p>
                                        @endif

                                        <div class="mt-1">

                                            <span class="text-3xl font-black text-slate-900">
                                                Rp {{ number_format($price, 0, ',', '.') }}
                                            </span>

                                        </div>

                                    </div>


                                    {{-- Info --}}
                                    <div class="mt-6 space-y-3">

                                        <div class="flex items-center gap-3 text-sm text-slate-600">

                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                                                <i class="fa-solid fa-calendar-check"></i>
                                            </div>

                                            <span>
                                                Aktif {{ $duration->duration_days }} hari
                                            </span>

                                        </div>

                                        <div class="flex items-center gap-3 text-sm text-slate-600">

                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                                <i class="fa-solid fa-check"></i>
                                            </div>

                                            <span>
                                                Aktif setelah pembayaran
                                            </span>

                                        </div>

                                    </div>


                                    {{-- Button --}}
                                    <div class="mt-auto pt-8">

                                        <a href="{{ route('public.subscription.summary', [$package->slug, encryptId($duration->id)]) }}"
                                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                            <span>
                                                Pilih Durasi
                                            </span>

                                            <i class="fa-solid fa-arrow-right"></i>

                                        </a>

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>
                @endif

            </div>

        </section>

    </div>

@endsection
