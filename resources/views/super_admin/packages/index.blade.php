@extends('layouts.admin')

@section('header')

<div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        Kelola Paket
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Kelola paket berlangganan yang tersedia untuk pedagang.
    </p>
</div>

@endsection

@section('content')

<div class="space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-lg font-extrabold text-slate-900">
                Daftar Paket
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pantau dan kelola paket berlangganan yang tersedia.
            </p>
        </div>

        <a
            href="{{ route('super_admin.packages.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Paket
        </a>

    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">

            <p class="mb-2 text-sm font-bold text-rose-700">
                Silakan perbaiki kesalahan berikut:
            </p>

            <ul class="list-inside list-disc space-y-1 text-sm text-rose-600">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Package Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        @if ($packages->count())

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1000px] text-left">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                No
                            </th>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Paket
                            </th>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Deskripsi
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Durasi
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach ($packages as $index => $package)

                            <tr class="transition hover:bg-slate-50/70">

                                {{-- No --}}
                                <td class="px-6 py-5 text-sm text-slate-500">
                                    {{ $packages->firstItem() + $index }}
                                </td>


                                {{-- Package --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                            <i class="fa-solid fa-box"></i>

                                        </div>


                                        <div class="min-w-0">

                                            <div class="flex items-center gap-2">

                                                <p class="font-bold text-slate-900">
                                                    {{ strip_tags($package->name) }}
                                                </p>

                                                @if ($package->badge)

                                                    <span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold uppercase text-indigo-600">

                                                        {{ strip_tags($package->badge) }}

                                                    </span>

                                                @endif

                                            </div>

                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ strip_tags($package->slug) }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Description --}}
                                <td class="max-w-xs px-6 py-5">

                                    <p class="line-clamp-2 text-sm text-slate-600">
                                        {{ $package->description ? strip_tags($package->description) : 'Tidak ada deskripsi.' }}
                                    </p>

                                </td>


                                {{-- Duration --}}
                                <td class="px-6 py-5 text-center">

                                    <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">

                                        {{ $package->durations_count }}

                                    </span>

                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-5 text-center">

                                    @if ($package->status === 'active')

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


                                {{-- Actions --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Duration --}}
                                        <a
                                            href="{{ route('super_admin.packages.durations.index', encryptId($package->id)) }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition hover:bg-slate-200"
                                            title="Kelola Durasi"
                                        >
                                            <i class="fa-solid fa-clock text-xs"></i>
                                        </a>


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('super_admin.packages.edit', encryptId($package->id)) }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                            title="Edit Paket"
                                        >
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('super_admin.packages.destroy', encryptId($package->id)) }}"
                                            method="POST"
                                            class="delete-package-form"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                                title="Hapus Paket"
                                            >
                                                <i class="fa-solid fa-trash text-xs"></i>
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
            @if ($packages->hasPages())

                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $packages->links() }}
                </div>

            @endif


        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                    <i class="fa-solid fa-box text-xl"></i>

                </div>

                <h3 class="mt-4 text-sm font-extrabold text-slate-700">
                    Belum ada paket
                </h3>

                <p class="mt-1 text-xs text-slate-400">
                    Belum ada paket berlangganan yang tersedia.
                </p>

                <a
                    href="{{ route('super_admin.packages.create') }}"
                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Paket
                </a>

            </div>

        @endif

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('js/super_admin/package.js') }}"></script>

@endpush
