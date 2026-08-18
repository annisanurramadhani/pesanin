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

                <span class="text-sm font-bold text-slate-500">
                    Pembayaran
                </span>

            </div>
        </header>


        {{-- Content --}}
        <main class="px-6 py-14">

            <div class="mx-auto max-w-4xl">

                {{-- Heading --}}
                <div class="mb-10 text-center">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">

                        Pembayaran

                    </span>

                    <h2 class="mt-5 text-3xl font-black tracking-tight text-slate-900 md:text-4xl">

                        Selesaikan Pembayaran

                    </h2>

                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500">

                        Periksa kembali invoice sebelum melanjutkan pembayaran.

                    </p>

                </div>


                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    {{-- Detail --}}
                    <div class="lg:col-span-2">

                        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-200 px-6 py-5">

                                <div class="flex items-center justify-between gap-4">

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                            Invoice
                                        </p>

                                        <h3 class="mt-1 text-lg font-black text-slate-900">
                                            {{ $subscription->invoice_number }}
                                        </h3>

                                    </div>

                                    <span
                                        class="rounded-full bg-amber-50 px-3 py-1.5 text-xs font-extrabold uppercase text-amber-600">

                                        Pending

                                    </span>

                                </div>

                            </div>


                            <div class="space-y-6 p-6">

                                {{-- Toko --}}
                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                        <i class="fa-solid fa-store"></i>

                                    </div>

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                            Toko
                                        </p>

                                        <p class="mt-1 text-lg font-extrabold text-slate-900">
                                            {{ $subscription->merchant->name }}
                                        </p>

                                    </div>

                                </div>


                                <div class="border-t border-slate-100"></div>


                                {{-- Paket --}}
                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500">

                                        <i class="fa-solid fa-box"></i>

                                    </div>

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                            Paket
                                        </p>

                                        <p class="mt-1 text-lg font-extrabold text-slate-900">
                                            {{ $subscription->packageDuration->package->name }}
                                        </p>

                                    </div>

                                </div>


                                <div class="border-t border-slate-100"></div>


                                {{-- Durasi --}}
                                <div class="flex items-center gap-4">

                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">

                                        <i class="fa-solid fa-calendar-days"></i>

                                    </div>

                                    <div>

                                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                            Durasi
                                        </p>

                                        <p class="mt-1 text-lg font-extrabold text-slate-900">
                                            {{ $subscription->packageDuration->name }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $subscription->packageDuration->duration_days }} hari
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Payment --}}
                    <div>

                        <div class="sticky top-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                            <div class="border-b border-slate-200 px-6 py-5">

                                <h3 class="text-lg font-extrabold text-slate-900">
                                    Total Pembayaran
                                </h3>

                            </div>


                            <div class="p-6">

                                <div class="flex items-center justify-between">

                                    <span class="text-sm text-slate-500">
                                        Total
                                    </span>

                                    <span class="text-2xl font-black text-slate-900">
                                        Rp {{ number_format($subscription->price, 0, ',', '.') }}
                                    </span>

                                </div>


                                <div class="my-6 border-t border-slate-200"></div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <div class="flex gap-3">

                                        <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>

                                        <p class="text-xs leading-5 text-slate-500">

                                            Setelah pembayaran berhasil,
                                            subscription akan otomatis menjadi
                                            <strong>Active</strong>.

                                        </p>

                                    </div>

                                </div>


                                <form
                                    method="POST"
                                    action="{{ route('payment.pay') }}"
                                    class="mt-6">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                        <i class="fa-solid fa-credit-card"></i>

                                        Konfirmasi Pembayaran

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </main>

    </div>

@endsection
