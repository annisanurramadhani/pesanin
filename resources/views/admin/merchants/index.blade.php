@extends('layouts.admin')

@section('header')

<div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        Kelola Merchant
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Kelola seluruh merchant yang terdaftar di platform PesanIn.
    </p>
</div>

@endsection

@section('content')

<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-lg font-extrabold text-slate-900">
                Daftar Merchant
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pantau informasi merchant, pengguna, dan langganan.
            </p>
        </div>

        <a
            href="{{ route('admin.merchants.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Merchant
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
                            No. HP
                        </th>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Pengguna
                        </th>

                        <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                            Langganan
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

                    @forelse ($merchants as $merchant)

                        @php
                            $subscription = $merchant->activeSubscription;
                            $users = $merchant->users;
                        @endphp

                        <tr class="transition hover:bg-slate-50/70">

                            <td class="px-6 py-5">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-amber-50 text-amber-500">

                                        @if ($merchant->logo)

                                            <img
                                                src="{{ asset('storage/' . $merchant->logo) }}"
                                                alt="{{ $merchant->name }}"
                                                class="h-full w-full object-cover"
                                            >

                                        @else

                                            <i class="fa-solid fa-store"></i>

                                        @endif

                                    </div>

                                    <div>

                                        <p class="font-bold text-slate-900">
                                            {{ $merchant->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $merchant->slug }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            <td class="px-6 py-5 text-sm text-slate-600">
                                {{ $merchant->phone ?? '-' }}
                            </td>

                            <td class="px-6 py-5">

                                <div>

                                    <p class="font-bold text-slate-800">
                                        {{ $users->count() }} Pengguna
                                    </p>

                                    @if ($users->count())

                                        <div class="mt-1 flex flex-wrap gap-1">

                                            @foreach ($users as $user)

                                                <span class="rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase text-slate-500">
                                                    {{ $user->role }}
                                                </span>

                                            @endforeach

                                        </div>

                                    @else

                                        <p class="mt-1 text-xs text-slate-400">
                                            Belum ada pengguna
                                        </p>

                                    @endif

                                </div>

                            </td>

                            <td class="px-6 py-5">

                                @if ($subscription)

                                    <div>

                                        <p class="font-bold text-slate-800">
                                            {{ $subscription->packageDuration->package->name ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $subscription->packageDuration->name ?? '-' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $subscription->start_date?->format('d M Y') }}
                                            -
                                            {{ $subscription->end_date?->format('d M Y') }}
                                        </p>

                                    </div>

                                @else

                                    <span class="text-sm font-semibold text-slate-400">
                                        Belum berlangganan
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-5">

                                @if ($merchant->status === 'active')

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600">

                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                        Aktif

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-600">

                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>

                                        Nonaktif

                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-5">

                                <div class="flex items-center justify-center gap-2">

                                    <a
                                        href="{{ route('admin.merchants.edit', [
                                            'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $merchant->id),
                                        ]) }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                        title="Edit Merchant"
                                    >
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>

                                    <form
                                        action="{{ route('admin.merchants.destroy', [
                                            'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $merchant->id),
                                        ]) }}"
                                        method="POST"
                                        class="delete-merchant-form"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                            title="Hapus Merchant"
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
                                    <i class="fa-solid fa-store text-xl"></i>
                                </div>

                                <h3 class="mt-4 text-sm font-extrabold text-slate-700">
                                    Belum ada merchant
                                </h3>

                                <p class="mt-1 text-xs text-slate-400">
                                    Belum ada merchant yang terdaftar di platform PesanIn.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($merchants->hasPages())

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $merchants->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
