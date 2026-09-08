@extends('layouts.merchant')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                Tambah Voucher
            </h2>

            <p class="text-xs font-medium text-slate-500 mt-1">
                Buat voucher baru untuk pelanggan Anda.
            </p>
        </div>

        <a
            href="{{ route('merchant.voucher.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-100 text-slate-700 text-sm font-extrabold hover:bg-slate-200 transition"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>
@endsection


@section('content')

<div class="max-w-4xl mx-auto">

    <form
        method="POST"
        action="{{ route('merchant.voucher.store') }}"
        class="space-y-6"
    >
        @csrf

        {{-- INFORMASI VOUCHER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-slate-900">
                    Informasi Voucher
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Tentukan kode dan jenis promo yang akan diberikan.
                </p>
            </div>


            <div class="p-6 space-y-6">

                {{-- KODE --}}
                <div>
                    <label
                        for="code"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Kode Voucher
                    </label>

                    <input
                        type="text"
                        name="code"
                        id="code"
                        value="{{ old('code') }}"
                        placeholder="Contoh: HEMAT10"
                        maxlength="50"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 uppercase outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                    >

                    <p class="text-[11px] text-slate-400 mt-2">
                        Kode yang akan dimasukkan pelanggan saat checkout.
                    </p>

                    @error('code')
                        <p class="text-xs text-rose-500 font-semibold mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- TIPE & NILAI --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- TIPE --}}
                    <div>
                        <label
                            for="type"
                            class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                        >
                            Tipe Diskon
                        </label>

                        <select
                            name="type"
                            id="type"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        >
                            <option value="percentage" {{ old('type', 'percentage') === 'percentage' ? 'selected' : '' }}>
                                Persentase (%)
                            </option>

                            <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>
                                Nominal Tetap (Rp)
                            </option>
                        </select>

                        @error('type')
                            <p class="text-xs text-rose-500 font-semibold mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- NILAI --}}
                    <div>
                        <label
                            for="value"
                            class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                        >
                            Nilai Diskon
                        </label>

                        <input
                            type="number"
                            name="value"
                            id="value"
                            value="{{ old('value') }}"
                            min="1"
                            step="0.01"
                            placeholder="Contoh: 10"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        >

                        <p
                            id="valueHelp"
                            class="text-[11px] text-slate-400 mt-2"
                        >
                            Masukkan persentase diskon.
                        </p>

                        @error('value')
                            <p class="text-xs text-rose-500 font-semibold mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

            </div>

        </div>


        {{-- ATURAN VOUCHER --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-slate-900">
                    Aturan Voucher
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Tentukan syarat penggunaan voucher.
                </p>
            </div>


            <div class="p-6 space-y-6">

                {{-- MINIMUM PEMBELIAN --}}
                <div>
                    <label
                        for="min_order_amount"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Minimal Pembelian
                    </label>

                    <input
                        type="number"
                        name="min_order_amount"
                        id="min_order_amount"
                        value="{{ old('min_order_amount', 0) }}"
                        min="0"
                        step="0.01"
                        placeholder="Contoh: 30000"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                    >

                    <p class="text-[11px] text-slate-400 mt-2">
                        Minimal total pesanan sebelum diskon agar voucher dapat digunakan.
                    </p>

                    @error('min_order_amount')
                        <p class="text-xs text-rose-500 font-semibold mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- MAX DISCOUNT --}}
                <div>
                    <label
                        for="max_discount_amount"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Maksimal Diskon
                    </label>

                    <input
                        type="number"
                        name="max_discount_amount"
                        id="max_discount_amount"
                        value="{{ old('max_discount_amount') }}"
                        min="0"
                        step="0.01"
                        placeholder="Contoh: 20000"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                    >

                    <p class="text-[11px] text-slate-400 mt-2">
                        Batas maksimal potongan harga yang dapat diberikan.
                    </p>

                    @error('max_discount_amount')
                        <p class="text-xs text-rose-500 font-semibold mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- BATAS PENGGUNAAN --}}
                <div>
                    <label
                        for="usage_limit"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Batas Penggunaan
                    </label>

                    <input
                        type="number"
                        name="usage_limit"
                        id="usage_limit"
                        value="{{ old('usage_limit') }}"
                        min="1"
                        step="1"
                        placeholder="Contoh: 100"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                    >

                    <p class="text-[11px] text-slate-400 mt-2">
                        Tentukan jumlah maksimal voucher yang dapat digunakan pelanggan.
                    </p>

                    @error('usage_limit')
                        <p class="text-xs text-rose-500 font-semibold mt-2">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

        </div>


        {{-- MASA BERLAKU --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-slate-900">
                    Masa Berlaku
                </h3>

                <p class="text-xs text-slate-500 mt-1">
                    Atur kapan voucher mulai dan berakhir.
                </p>
            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- MULAI --}}
                    <div>
                        <label
                            for="starts_at"
                            class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                        >
                            Mulai Berlaku
                        </label>

                        <input
                            type="datetime-local"
                            name="starts_at"
                            id="starts_at"
                            value="{{ old('starts_at') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        >

                        <p class="text-[11px] text-slate-400 mt-2">
                            Tentukan tanggal dan waktu mulai voucher dapat digunakan.
                        </p>

                        @error('starts_at')
                            <p class="text-xs text-rose-500 font-semibold mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- BERAKHIR --}}
                    <div>
                        <label
                            for="expires_at"
                            class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                        >
                            Berakhir
                        </label>

                        <input
                            type="datetime-local"
                            name="expires_at"
                            id="expires_at"
                            value="{{ old('expires_at') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        >

                        <p class="text-[11px] text-slate-400 mt-2">
                            Tentukan tanggal dan waktu terakhir voucher dapat digunakan.
                        </p>

                        @error('expires_at')
                            <p class="text-xs text-rose-500 font-semibold mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

            </div>

        </div>


        {{-- STATUS --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="text-base font-extrabold text-slate-900">
                    Status Voucher
                </h3>
            </div>


            <div class="p-6">

                <label class="flex items-center gap-3 cursor-pointer">

                    <input
                        type="checkbox"
                        name="status"
                        value="active"
                        {{ old('status', 'active') === 'active' ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                    >

                    <div>
                        <p class="text-sm font-extrabold text-slate-800">
                            Aktifkan voucher
                        </p>

                        <p class="text-xs text-slate-400 mt-0.5">
                            Voucher dapat digunakan pelanggan jika status aktif.
                        </p>
                    </div>

                </label>

            </div>

        </div>


        {{-- ACTION --}}
        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('merchant.voucher.index') }}"
                class="px-5 py-3 rounded-xl bg-slate-100 text-slate-700 text-sm font-extrabold hover:bg-slate-200 transition"
            >
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-500 text-slate-950 text-sm font-extrabold shadow-lg shadow-amber-500/20 hover:bg-amber-400 transition"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Voucher
            </button>

        </div>

    </form>

</div>


{{-- UPDATE LABEL NILAI DISKON --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const type = document.getElementById('type');
        const value = document.getElementById('value');
        const valueHelp = document.getElementById('valueHelp');

        function updateValueHelp() {

            if (type.value === 'percentage') {

                value.placeholder = 'Contoh: 10';

                valueHelp.textContent =
                    'Masukkan persentase diskon. Contoh: 10 = diskon 10%.';

            } else {

                value.placeholder = 'Contoh: 10000';

                valueHelp.textContent =
                    'Masukkan nominal diskon dalam Rupiah. Contoh: 10000 = Rp 10.000.';
            }
        }

        type.addEventListener('change', updateValueHelp);

        updateValueHelp();

    });
</script>

@endsection