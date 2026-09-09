@extends('layouts.admin')

@section('header')

<div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        Kelola Diskon
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Kelola promo dan diskon untuk paket berlangganan.
    </p>
</div>

@endsection


@section('content')

<div class="space-y-6">

    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-lg font-extrabold text-slate-900">
                Daftar Promo
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pantau dan kelola promo subscription yang sedang tersedia.
            </p>
        </div>


        {{-- Tambah Promo --}}
        <a
            href="{{ route('super_admin.subscription_promotions.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Promo
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


    {{-- Success Message --}}
    @if (session('success'))

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">

            <div class="flex items-center gap-2">

                <i class="fa-solid fa-circle-check text-emerald-500"></i>

                <p class="text-sm font-semibold text-emerald-700">
                    {{ session('success') }}
                </p>

            </div>

        </div>

    @endif


    {{-- Promotion Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        @if ($promotions->count())

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1100px] text-left">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                No
                            </th>

                            <th class="px-6 py-4 text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Promo
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Diskon
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Durasi Paket
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                Masa Berlaku
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

                        @foreach ($promotions as $index => $promotion)

                            <tr class="transition hover:bg-slate-50/70">

                                {{-- No --}}
                                <td class="px-6 py-5 text-sm text-slate-500">
                                    {{ $promotions->firstItem() + $index }}
                                </td>


                                {{-- Promotion --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                            <i class="fa-solid fa-tags"></i>

                                        </div>


                                        <div class="min-w-0">

                                            <p class="font-bold text-slate-900">
                                                {{ strip_tags($promotion->name) }}
                                            </p>

                                            <p class="mt-1 max-w-xs truncate text-xs text-slate-400">
                                                {{ $promotion->description
                                                    ? strip_tags($promotion->description)
                                                    : 'Tidak ada deskripsi.'
                                                }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Discount --}}
                                <td class="px-6 py-5 text-center">

                                    @if ($promotion->discount_type === 'percentage')

                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-600">

                                            -{{ rtrim(
                                                rtrim(
                                                    number_format(
                                                        $promotion->discount_value,
                                                        2,
                                                        ',',
                                                        '.'
                                                    ),
                                                    '0'
                                                ),
                                                ','
                                            ) }}%

                                        </span>

                                    @else

                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600">

                                            - Rp {{ number_format(
                                                $promotion->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </span>

                                    @endif

                                </td>


                                {{-- Duration Count --}}
                                <td class="px-6 py-5 text-center">

                                    <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">

                                        {{ $promotion->durations_count }}

                                    </span>

                                </td>


                                {{-- Validity --}}
                                <td class="px-6 py-5">

                                    <div class="text-center">

                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ $promotion->starts_at->format('d M Y H:i') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            sampai
                                            {{ $promotion->ends_at->format('d M Y H:i') }}
                                        </p>

                                    </div>

                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-5 text-center">

                                    @if ($promotion->status === 'active')

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

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route(
                                                'super_admin.subscription_promotions.edit',
                                                encryptId($promotion->id)
                                            ) }}"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100"
                                            title="Edit Promo"
                                        >
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route(
                                                'super_admin.subscription_promotions.destroy',
                                                encryptId($promotion->id)
                                            ) }}"
                                            method="POST"
                                            class="delete-promotion-form"
                                        >

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                                title="Hapus Promo"
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
            @if ($promotions->hasPages())

                <div class="border-t border-slate-200 px-6 py-4">

                    {{ $promotions->links() }}

                </div>

            @endif


        @else

            {{-- Empty State --}}
            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                    <i class="fa-solid fa-tags text-xl"></i>

                </div>


                <h3 class="mt-4 text-sm font-extrabold text-slate-700">
                    Belum ada promo
                </h3>


                <p class="mt-1 text-xs text-slate-400">
                    Belum ada promo diskon subscription yang tersedia.
                </p>


                <a
                    href="{{ route('super_admin.subscription_promotions.create') }}"
                    class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Promo
                </a>

            </div>

        @endif

    </div>

</div>

@endsection
