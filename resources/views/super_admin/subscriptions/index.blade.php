@extends('layouts.admin')

@section('header')

<div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        Kelola Langganan
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Kelola seluruh langganan merchant di platform PesanIn.
    </p>
</div>

@endsection

@section('content')

<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-lg font-extrabold text-slate-900">
                Daftar Langganan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pantau paket, periode, harga, dan status langganan merchant.
            </p>
        </div>

        <a
            href="{{ route('super_admin.subscriptions.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Langganan
        </a>

    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1100px] text-left">

                <thead class="border-b border-slate-200 bg-slate-50">

                    <tr>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Merchant
                        </th>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Paket
                        </th>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Periode
                        </th>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Harga
                        </th>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($subscriptions as $subscription)

                        <tr class="transition hover:bg-slate-50/70">

                            <td class="px-6 py-5">

                                <div>

                                    <p class="font-bold text-slate-900">
                                        {{ $subscription->merchant->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $subscription->merchant->slug ?? '-' }}
                                    </p>

                                </div>

                            </td>

                            <td class="px-6 py-5">

                                <div>

                                    <p class="font-bold text-slate-800">
                                        {{ $subscription->packageDuration->package->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $subscription->packageDuration->name ?? '-' }}
                                    </p>

                                </div>

                            </td>

                            <td class="px-6 py-5">

                                <p class="text-sm font-semibold text-slate-700">
                                    {{ $subscription->start_date?->format('d M Y') }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    sampai {{ $subscription->end_date?->format('d M Y') }}
                                </p>

                            </td>

                            <td class="px-6 py-5 text-sm font-bold text-slate-800">
                                Rp {{ number_format($subscription->price, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-5">

                                @if ($subscription->status === 'active')

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>

                                @elseif ($subscription->status === 'expired')

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                        Kedaluwarsa
                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                        Dibatalkan
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    <a
                                        href="{{ route('super_admin.subscriptions.show', [
                                            'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $subscription->id),
                                        ]) }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200"
                                        title="Lihat Detail"
                                    >
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    <a
                                        href="{{ route('super_admin.subscriptions.edit', [
                                            'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $subscription->id),
                                        ]) }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                        title="Edit"
                                    >
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>

                                    <form
                                        action="{{ route('super_admin.subscriptions.destroy', [
                                            'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $subscription->id),
                                        ]) }}"
                                        method="POST"
                                        class="delete-subscription-form"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                            title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="px-6 py-16 text-center">

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fa-solid fa-credit-card text-xl"></i>
                                </div>

                                <h3 class="mt-4 text-sm font-extrabold text-slate-700">
                                    Belum ada langganan
                                </h3>

                                <p class="mt-1 text-xs text-slate-400">
                                    Tambahkan langganan untuk merchant yang sudah terdaftar.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($subscriptions->hasPages())

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $subscriptions->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
