@extends('layouts.merchant')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center gap-4 mb-8">
        <a
            href="{{ route('merchant.voucher.index') }}"
            class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition"
        >
            <i class="fa-solid fa-arrow-left text-slate-600"></i>
        </a>

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Edit Voucher
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Ubah informasi voucher yang sudah dibuat.
            </p>
        </div>
    </div>


    {{-- FORM --}}
    <form
        method="POST"
        action="{{ route('merchant.voucher.update', $encryptedId) }}"
        class="space-y-6"
    >
        @csrf
        @method('PUT')


        {{-- INFORMASI VOUCHER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800">
                    Informasi Voucher
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Tentukan kode dan jenis diskon voucher.
                </p>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- KODE --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Kode Voucher
                    </label>

                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', $voucher->code) }}"
                        maxlength="50"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 uppercase focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                        placeholder="Contoh: HEMAT10"
                    >

                    @error('code')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- TIPE --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Tipe Diskon
                    </label>

                    <select
                        name="type"
                        id="voucherType"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                    >
                        <option
                            value="percentage"
                            {{ old('type', $voucher->type) === 'percentage' ? 'selected' : '' }}
                        >
                            Persentase (%)
                        </option>

                        <option
                            value="fixed"
                            {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}
                        >
                            Nominal (Rp)
                        </option>
                    </select>

                    @error('type')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- NILAI --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nilai Diskon
                    </label>

                    <input
                        type="number"
                        name="value"
                        id="voucherValue"
                        value="{{ old('value', $voucher->value) }}"
                        min="1"
                        step="0.01"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                    >

                    <p
                        id="voucherValueHelp"
                        class="text-xs text-slate-400 mt-1"
                    >
                        Masukkan persentase diskon.
                    </p>

                    @error('value')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>


        {{-- ATURAN VOUCHER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800">
                    Aturan Voucher
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Atur ketentuan penggunaan voucher.
                </p>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- MIN ORDER --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Minimal Pembelian
                    </label>

                    <input
                        type="number"
                        name="min_order_amount"
                        value="{{ old('min_order_amount', $voucher->min_order_amount) }}"
                        min="0"
                        step="0.01"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                        placeholder="30000"
                    >

                    @error('min_order_amount')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- MAX DISCOUNT --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Maksimal Diskon
                    </label>

                    <input
                        type="number"
                        name="max_discount_amount"
                        value="{{ old('max_discount_amount', $voucher->max_discount_amount) }}"
                        min="0"
                        required
                        step="0.01"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                        placeholder="10000"
                    >

                    <p class="text-xs text-slate-400 mt-1">
                        Tentukan batas maksimal potongan harga yang dapat diberikan.
                    </p>

                    @error('max_discount_amount')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- USAGE LIMIT --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Batas Penggunaan
                    </label>

                    <input
                        type="number"
                        name="usage_limit"
                        value="{{ old('usage_limit', $voucher->usage_limit) }}"
                        min="1"
                        step="1"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                        placeholder="100"
                    >

                    <p class="text-xs text-slate-400 mt-1">
                        Tentukan jumlah maksimal voucher yang dapat digunakan pelanggan.
                    </p>

                    @error('usage_limit')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>


        {{-- MASA BERLAKU --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800">
                    Masa Berlaku
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Tentukan kapan voucher mulai dan berakhir.
                </p>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- START --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Mulai
                    </label>

                    <input
                        type="datetime-local"
                        name="starts_at"
                        value="{{ old('starts_at', $voucher->starts_at?->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                    >

                    @error('starts_at')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- EXPIRES --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Berakhir
                    </label>

                    <input
                        type="datetime-local"
                        name="expires_at"
                        value="{{ old('expires_at', $voucher->expires_at?->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none"
                    >

                    @error('expires_at')
                        <p class="text-sm text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>
        </div>


        {{-- STATUS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Status Voucher
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Aktifkan voucher agar dapat digunakan pelanggan.
                    </p>
                </div>

                <label class="relative inline-flex items-center cursor-pointer">

                    <input
                        type="checkbox"
                        name="status"
                        value="active"
                        class="sr-only peer"
                        {{ old('status', $voucher->status) === 'active' ? 'checked' : '' }}
                    >

                    <div class="w-12 h-7 bg-slate-300 rounded-full peer peer-checked:bg-amber-500 transition"></div>

                    <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow transition peer-checked:translate-x-5"></div>

                </label>

            </div>
        </div>


        {{-- BUTTON --}}
        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('merchant.voucher.index') }}"
                class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50 transition"
            >
                Batal
            </a>

            <button
                type="submit"
                class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold shadow-lg shadow-amber-500/20 transition"
            >
                <i class="fa-solid fa-save mr-2"></i>
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>


{{-- SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const type = document.getElementById('voucherType');
        const value = document.getElementById('voucherValue');
        const help = document.getElementById('voucherValueHelp');

        function updateVoucherValue() {

            if (type.value === 'percentage') {

                value.placeholder = '10';
                value.max = '100';

                help.textContent =
                    'Masukkan persentase diskon, maksimal 100%.';

            } else {

                value.placeholder = '10000';
                value.removeAttribute('max');

                help.textContent =
                    'Masukkan nominal potongan dalam Rupiah.';

            }
        }

        type.addEventListener(
            'change',
            updateVoucherValue
        );

        updateVoucherValue();

    });
</script>

@endsection