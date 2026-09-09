@php
    use Illuminate\Support\Facades\Crypt;
@endphp
@extends('layouts.merchant')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                Kelola Voucher
            </h2>

            <p class="text-xs font-medium text-slate-500 mt-1">
                Kelola voucher dan promo untuk pelanggan Anda.
            </p>
        </div>

        <a
            href="{{ route('merchant.voucher.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-amber-500 text-slate-950 text-sm font-extrabold shadow-lg shadow-amber-500/20 hover:bg-amber-400 transition"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Voucher
        </a>
    </div>
@endsection

@section('content')

    {{-- CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- CARD HEADER --}}
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">

            <div>
                <h3 class="text-base font-extrabold text-slate-900">
                    Daftar Voucher
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Voucher yang tersedia untuk merchant
                    {{ $merchant->name }}.
                </p>
            </div>

            <div class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-extrabold">
                {{ $vouchers->count() }} Voucher
            </div>

        </div>


        {{-- TABLE --}}
        @if ($vouchers->count() > 0)

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-slate-50 border-b border-slate-200">

                        <tr>

                            <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Kode
                            </th>

                            <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Diskon
                            </th>

                            <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Min. Pembelian
                            </th>

                            <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Masa Berlaku
                            </th>

                            <th class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Penggunaan
                            </th>

                            <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-center text-[11px] font-black uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @foreach ($vouchers as $voucher)

                            <tr class="hover:bg-slate-50/70 transition"
                                data-voucher-id="{{ $voucher->id }}" >

                                {{-- KODE --}}
                                <td class="px-6 py-4">

                                    <div class="font-extrabold text-slate-900">
                                        {{ $voucher->code }}
                                    </div>

                                </td>


                                {{-- DISKON --}}
                                <td class="px-6 py-4">

                                    @if ($voucher->type === 'percentage')

                                        <span class="font-extrabold text-amber-600">
                                            {{ number_format($voucher->value, 0, ',', '.') }}%
                                        </span>

                                    @else

                                        <span class="font-extrabold text-amber-600">
                                            Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                        </span>

                                    @endif

                                    @if ($voucher->max_discount_amount)

                                        <div class="text-[11px] text-slate-400 mt-1">
                                            Maks. Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}
                                        </div>

                                    @endif

                                </td>


                                {{-- MINIMUM PEMBELIAN --}}
                                <td class="px-6 py-4">

                                    <span class="font-semibold text-slate-700">
                                        Rp {{ number_format($voucher->min_order_amount, 0, ',', '.') }}
                                    </span>

                                </td>


                                {{-- MASA BERLAKU --}}
                                <td class="px-6 py-4">

                                    @if ($voucher->starts_at || $voucher->expires_at)

                                        <div class="text-xs text-slate-700 space-y-1">

                                            @if ($voucher->starts_at)

                                                <div>
                                                    <span class="text-slate-400">
                                                        Mulai:
                                                    </span>

                                                    {{ $voucher->starts_at->format('d/m/Y H:i') }}
                                                </div>

                                            @endif

                                            @if ($voucher->expires_at)

                                                <div>
                                                    <span class="text-slate-400">
                                                        Berakhir:
                                                    </span>

                                                    {{ $voucher->expires_at->format('d/m/Y H:i') }}
                                                </div>

                                            @endif

                                        </div>

                                    @else

                                        <span class="text-xs text-slate-400">
                                            Tidak dibatasi
                                        </span>

                                    @endif

                                </td>


                                {{-- PENGGUNAAN --}}
                                <td class="px-6 py-4" 
                                    data-voucher-usage>

                                    <span class="font-semibold text-slate-700">
                                        {{ $voucher->used_count }}
                                    </span>

                                    @if (!is_null($voucher->usage_limit))

                                        <span class="text-slate-400">
                                            / {{ $voucher->usage_limit }}
                                        </span>

                                    @else

                                        <span class="text-xs text-slate-400 ml-1">
                                            Tidak terbatas
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}
                                <td class="px-6 py-4 text-center" 
                                    data-voucher-status>

                                    @if ($voucher->status === 'active')

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-black uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                {{-- AKSI --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <a
                                            href="{{ route('merchant.voucher.edit', Crypt::encryptString($voucher->id)) }}"
                                            class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-amber-100 hover:text-amber-600 transition"
                                            title="Edit Voucher"
                                        >
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>

                                        <button
                                            type="button"
                                            class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-rose-100 hover:text-rose-600 transition"
                                            title="Hapus Voucher"
                                            onclick="confirmDeleteVoucher({{ $voucher->id }})"
                                        >
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            {{-- EMPTY STATE --}}
            <div class="px-6 py-16 text-center">

                <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                    <i class="fa-solid fa-ticket text-2xl"></i>
                </div>

                <h3 class="mt-5 text-base font-extrabold text-slate-900">
                    Belum Ada Voucher
                </h3>

                <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">
                    Buat voucher pertama Anda untuk memberikan promo
                    kepada pelanggan.
                </p>

                <a
                    href="{{ route('merchant.voucher.create') }}"
                    class="inline-flex items-center gap-2 mt-6 px-4 py-3 rounded-xl bg-amber-500 text-slate-950 text-sm font-extrabold hover:bg-amber-400 transition"
                >
                    <i class="fa-solid fa-plus"></i>
                    Tambah Voucher
                </a>

            </div>

        @endif

    </div>

    <script src="{{ asset('js/merchant/voucher.js') }}"></script>
@endsection