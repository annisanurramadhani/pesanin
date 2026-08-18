@extends('layouts.admin')


@section('header')

<div class="flex items-center gap-4">

    {{-- Back --}}
    <a
        href="{{ route('super_admin.packages.index') }}"
        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800"
        title="Kembali ke Paket"
    >
        <i class="fa-solid fa-arrow-left"></i>
    </a>


    {{-- Header Title --}}
    <div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Kelola Durasi Paket
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola durasi dan harga untuk paket
            <span class="font-semibold text-slate-700">
                {{ strip_tags($package->name) }}
            </span>.
        </p>

    </div>

</div>

@endsection


@section('content')

<div class="mx-auto max-w-5xl">


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4">

            <div class="flex items-start gap-3">

                <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500"></i>

                <div>

                    <p class="text-sm font-bold text-rose-700">
                        Silakan perbaiki kesalahan berikut:
                    </p>

                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-rose-600">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- Package Information --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="p-6">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">


                {{-- Package --}}
                <div class="flex items-start gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                        <i class="fa-solid fa-box text-lg"></i>
                    </div>


                    <div>

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Paket
                        </p>

                        <div class="mt-1 flex flex-wrap items-center gap-3">

                            <h2 class="text-xl font-extrabold text-slate-900">
                                {{ strip_tags($package->name) }}
                            </h2>


                            {{-- Status --}}
                            @if ($package->status === 'active')

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600"
                                >

                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                    Aktif

                                </span>

                            @else

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500"
                                >

                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>

                                    Nonaktif

                                </span>

                            @endif

                        </div>


                        @if ($package->description)

                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                                {{ strip_tags($package->description) }}
                            </p>

                        @endif

                    </div>

                </div>


                {{-- Duration Count --}}
                <div class="shrink-0">

                    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <i class="fa-solid fa-clock"></i>
                        </div>

                        <div>

                            <p class="text-xs font-medium text-slate-400">
                                Total Durasi
                            </p>

                            <p class="text-sm font-extrabold text-slate-800">
                                {{ $durations->total() }} Durasi
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Duration Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


        {{-- Card Header --}}
        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fa-solid fa-clock"></i>
                    </div>

                    <div>

                        <h2 class="font-extrabold text-slate-900">
                            Daftar Durasi
                        </h2>

                        <p class="text-xs text-slate-400">
                            Kelola pilihan durasi dan harga berlangganan paket.
                        </p>

                    </div>

                </div>


                {{-- Add Duration --}}
                <a
                    href="{{ route('super_admin.packages.durations.create', [
                        'encryptedId' => encryptId($package->id)
                    ]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-plus"></i>

                    Tambah Durasi
                </a>

            </div>

        </div>


        {{-- Table --}}
        @if ($durations->count())

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr>

                            <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                No
                            </th>

                            <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Nama Durasi
                            </th>

                            <th class="whitespace-nowrap px-6 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Durasi
                            </th>

                            <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Harga
                            </th>

                            <th class="whitespace-nowrap px-6 py-4 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Harga Diskon
                            </th>

                            <th class="whitespace-nowrap px-6 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="whitespace-nowrap px-6 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach ($durations as $index => $duration)

                            <tr class="transition hover:bg-slate-50">


                                {{-- Number --}}
                                <td class="whitespace-nowrap px-6 py-4 text-slate-500">

                                    {{ $durations->firstItem() + $index }}

                                </td>


                                {{-- Duration Name --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                            <i class="fa-solid fa-clock"></i>
                                        </div>

                                        <div>

                                            <p class="font-bold text-slate-800">
                                                {{ strip_tags($duration->name) }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Durasi berlangganan
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Duration --}}
                                <td class="px-6 py-4 text-center">

                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">

                                        {{ $duration->duration_days }} hari

                                    </span>

                                </td>


                                {{-- Price --}}
                                <td class="whitespace-nowrap px-6 py-4">

                                    <span class="font-bold text-slate-800">

                                        Rp {{ number_format($duration->price, 0, ',', '.') }}

                                    </span>

                                </td>


                                {{-- Discount --}}
                                <td class="px-6 py-4">

                                    @if ($duration->discount_price !== null)

                                        <div>

                                            <span class="whitespace-nowrap font-bold text-emerald-600">

                                                Rp {{ number_format($duration->discount_price, 0, ',', '.') }}

                                            </span>


                                            @if ($duration->price > 0)

                                                @php

                                                    $discountPercentage =
                                                        (($duration->price - $duration->discount_price)
                                                        / $duration->price) * 100;

                                                @endphp

                                                <span class="mt-0.5 block text-xs font-medium text-emerald-500">

                                                    Hemat {{ round($discountPercentage) }}%

                                                </span>

                                            @endif

                                        </div>

                                    @else

                                        <span class="text-slate-400">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-4 text-center">

                                    @if ($duration->status === 'active')

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600"
                                        >

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Aktif

                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500"
                                        >

                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('super_admin.packages.durations.edit', [
                                                'encryptedId' => encryptId($package->id),
                                                'duration' => encryptId($duration->id),
                                            ]) }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition hover:bg-indigo-100"
                                            title="Edit Durasi"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('super_admin.packages.durations.destroy', [
                                                'encryptedId' => encryptId($package->id),
                                                'duration' => encryptId($duration->id),
                                            ]) }}"
                                            method="POST"
                                            class="delete-duration-form"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                                title="Hapus Durasi"
                                            >
                                                <i class="fa-solid fa-trash"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($durations->hasPages())

                <div class="border-t border-slate-200 px-6 py-4">

                    {{ $durations->links() }}

                </div>

            @endif


        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                    <i class="fa-solid fa-clock text-2xl"></i>

                </div>


                <h3 class="mt-5 text-lg font-extrabold text-slate-800">
                    Belum ada durasi paket
                </h3>


                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">

                    Tambahkan durasi dan harga untuk paket
                    <span class="font-bold text-slate-700">
                        {{ strip_tags($package->name) }}
                    </span>.

                </p>
                
            </div>

        @endif

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('js/super_admin/duration.js') }}"></script>

@endpush
