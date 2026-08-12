@extends('layouts.admin')

@section('header')

<div class="flex items-center gap-4">

    <a
        href="{{ route('admin.subscriptions.index') }}"
        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800"
    >
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Detail Langganan
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Informasi lengkap langganan merchant.
        </p>

    </div>

</div>

@endsection

@section('content')

<div class="mx-auto max-w-4xl">

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <i class="fa-solid fa-credit-card"></i>
                </div>

                <div>

                    <h2 class="font-extrabold text-slate-900">
                        {{ $subscription->merchant->name ?? '-' }}
                    </h2>

                    <p class="text-xs text-slate-400">
                        Detail informasi langganan
                    </p>

                </div>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Merchant
                </p>

                <p class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $subscription->merchant->name ?? '-' }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    {{ $subscription->merchant->phone ?? '-' }}
                </p>

            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Paket
                </p>

                <p class="mt-2 text-lg font-extrabold text-slate-900">
                    {{ $subscription->packageDuration->package->name ?? '-' }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    {{ $subscription->packageDuration->name ?? '-' }}
                </p>

            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Periode
                </p>

                <p class="mt-2 text-sm font-bold text-slate-800">
                    {{ $subscription->start_date?->format('d M Y') }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    sampai {{ $subscription->end_date?->format('d M Y') }}
                </p>

            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Harga
                </p>

                <p class="mt-2 text-lg font-extrabold text-slate-900">
                    Rp {{ number_format($subscription->price, 0, ',', '.') }}
                </p>

            </div>

            <div class="md:col-span-2">

                <div class="rounded-xl border border-slate-200 bg-white p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Status Langganan
                    </p>

                    <div class="mt-3">

                        @if ($subscription->status === 'active')

                            <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-600">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>

                        @elseif ($subscription->status === 'expired')

                            <span class="inline-flex items-center gap-2 rounded-full bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600">
                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                Kedaluwarsa
                            </span>

                        @else

                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-xs font-bold text-slate-600">
                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                Dibatalkan
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">

            <a
                href="{{ route('admin.subscriptions.index') }}"
                class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
            >
                Kembali
            </a>

            <a
                href="{{ route('admin.subscriptions.edit', [
                    'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $subscription->id),
                ]) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-amber-400"
            >
                <i class="fa-solid fa-pen"></i>
                Edit Langganan
            </a>

        </div>

    </div>

</div>

@endsection
