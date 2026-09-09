@extends('layouts.admin')

@section('header')

<div>
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        Tambah Promo
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Buat promo diskon untuk paket berlangganan.
    </p>
</div>

@endsection


@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-lg font-extrabold text-slate-900">
                Informasi Promo
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Isi informasi promo dan tentukan durasi paket yang mendapatkan diskon.
            </p>
        </div>

        <a
            href="{{ route('super_admin.subscription_promotions.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-5 py-3 text-sm font-extrabold text-slate-700 transition hover:bg-slate-200"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>

    </div>


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">

            <div class="flex items-start gap-3">

                <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500"></i>

                <div>

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

            </div>

        </div>

    @endif


    <form
        action="{{ route('super_admin.subscription_promotions.store') }}"
        method="POST"
        class="space-y-6"
    >

        @csrf


        {{-- Basic Information --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-extrabold text-slate-900">
                    Informasi Dasar
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Masukkan nama dan deskripsi promo.
                </p>

            </div>


            <div class="space-y-5 p-6">

                {{-- Name --}}
                <div>

                    <label
                        for="name"
                        class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-600"
                    >
                        Nama Promo
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        maxlength="100"
                        placeholder="Contoh: Promo Merdeka"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >

                    @error('name')

                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Description --}}
                <div>

                    <label
                        for="description"
                        class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-600"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        maxlength="1000"
                        placeholder="Masukkan deskripsi promo..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >{{ old('description') }}</textarea>

                    @error('description')

                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- Discount Settings --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-extrabold text-slate-900">
                    Pengaturan Diskon
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Tentukan jenis dan nilai diskon yang diberikan.
                </p>

            </div>


            <div class="space-y-5 p-6">

                {{-- Discount Type --}}
                <div>

                    <label class="mb-3 block text-xs font-extrabold uppercase tracking-wider text-slate-600">
                        Jenis Diskon
                        <span class="text-rose-500">*</span>
                    </label>


                    <div class="grid gap-4 sm:grid-cols-2">

                        {{-- Percentage --}}
                        <label
                            class="discount-type-option cursor-pointer rounded-xl border border-slate-200 p-4 transition hover:border-amber-300"
                        >

                            <div class="flex items-start gap-3">

                                <input
                                    type="radio"
                                    name="discount_type"
                                    value="percentage"
                                    {{ old('discount_type', 'percentage') === 'percentage' ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 border-slate-300 text-amber-500 focus:ring-amber-400"
                                >

                                <div>

                                    <p class="text-sm font-bold text-slate-900">
                                        Persentase
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Contoh diskon 10%, 20%, atau 50%.
                                    </p>

                                </div>

                            </div>

                        </label>


                        {{-- Fixed --}}
                        <label
                            class="discount-type-option cursor-pointer rounded-xl border border-slate-200 p-4 transition hover:border-amber-300"
                        >

                            <div class="flex items-start gap-3">

                                <input
                                    type="radio"
                                    name="discount_type"
                                    value="fixed"
                                    {{ old('discount_type') === 'fixed' ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 border-slate-300 text-amber-500 focus:ring-amber-400"
                                >

                                <div>

                                    <p class="text-sm font-bold text-slate-900">
                                        Nominal Tetap
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Contoh potongan Rp 10.000.
                                    </p>

                                </div>

                            </div>

                        </label>

                    </div>

                    @error('discount_type')

                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Discount Value --}}
                <div>

                    <label
                        for="discount_value"
                        class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-600"
                    >
                        Nilai Diskon
                        <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">

                        <input
                            type="number"
                            id="discount_value"
                            name="discount_value"
                            value="{{ old('discount_value') }}"
                            required
                            min="0"
                            step="0.01"
                            placeholder="Masukkan nilai diskon"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-16 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                        >

                        <span
                            id="discount-unit"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400"
                        >
                            %
                        </span>

                    </div>

                    <p
                        id="discount-help"
                        class="mt-1.5 text-xs text-slate-400"
                    >
                        Masukkan persentase diskon antara 0 sampai 100%.
                    </p>

                    @error('discount_value')

                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- Promotion Period --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-extrabold text-slate-900">
                    Masa Berlaku
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Tentukan kapan promo mulai dan berakhir.
                </p>

            </div>


            <div class="grid gap-5 p-6 md:grid-cols-2">

                {{-- Start --}}
                <div>

                    <label
                        for="starts_at"
                        class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-600"
                    >
                        Mulai Promo
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="starts_at"
                        name="starts_at"
                        value="{{ old('starts_at') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >

                    @error('starts_at')

                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- End --}}
                <div>

                    <label
                        for="ends_at"
                        class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-600"
                    >
                        Berakhir Promo
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="datetime-local"
                        id="ends_at"
                        name="ends_at"
                        value="{{ old('ends_at') }}"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >

                    @error('ends_at')

                        <p class="mt-1.5 text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- Package Durations --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-extrabold text-slate-900">
                    Durasi Paket
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Pilih durasi paket yang akan mendapatkan promo ini.
                </p>

            </div>


            <div class="p-6">

                @if ($durations->count())

                    @php
                        $groupedDurations = $durations->groupBy(function ($duration) {
                            return $duration->package->id;
                        });
                    @endphp


                    <div class="space-y-5">

                        @foreach ($groupedDurations as $packageId => $packageDurations)

                            @php
                                $package = $packageDurations->first()->package;
                            @endphp

                            <div class="rounded-xl border border-slate-200">

                                {{-- Package Header --}}
                                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-500">

                                        <i class="fa-solid fa-box text-sm"></i>

                                    </div>

                                    <div>

                                        <p class="text-sm font-bold text-slate-900">
                                            {{ strip_tags($package->name) }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Pilih durasi yang mendapatkan promo
                                        </p>

                                    </div>

                                </div>


                                {{-- Durations --}}
                                <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3">

                                    @foreach ($packageDurations as $duration)

                                        <label
                                            class="duration-option flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-amber-300 hover:bg-amber-50/30"
                                        >

                                            <input
                                                type="checkbox"
                                                name="duration_ids[]"
                                                value="{{ $duration->id }}"
                                                {{ in_array($duration->id, old('duration_ids', [])) ? 'checked' : '' }}
                                                class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400"
                                            >

                                            <div class="min-w-0">

                                                <p class="text-sm font-bold text-slate-800">
                                                    {{ strip_tags($duration->name) }}
                                                </p>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ $duration->duration_days }} hari
                                                </p>

                                                <p class="mt-1 text-xs font-semibold text-slate-700">
                                                    Rp {{ number_format($duration->price, 0, ',', '.') }}
                                                </p>

                                            </div>

                                        </label>

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">

                        <div class="flex items-start gap-3">

                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-500"></i>

                            <div>

                                <p class="text-sm font-bold text-amber-700">
                                    Belum ada durasi paket aktif
                                </p>

                                <p class="mt-1 text-xs text-amber-600">
                                    Tambahkan durasi paket aktif terlebih dahulu sebelum membuat promo.
                                </p>

                            </div>

                        </div>

                    </div>

                @endif


                @error('duration_ids')

                    <p class="mt-3 text-xs font-medium text-rose-600">
                        {{ $message }}
                    </p>

                @enderror


                @error('duration_ids.*')

                    <p class="mt-1.5 text-xs font-medium text-rose-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>


        {{-- Status --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h3 class="text-base font-extrabold text-slate-900">
                    Status Promo
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Tentukan apakah promo dapat digunakan.
                </p>

            </div>


            <div class="p-6">

                <label class="flex cursor-pointer items-center gap-4">

                    <input
                        type="checkbox"
                        name="status"
                        value="active"
                        {{ old('status', 'active') === 'active' ? 'checked' : '' }}
                        class="h-5 w-5 rounded border-slate-300 text-amber-500 focus:ring-amber-400"
                    >

                    <div>

                        <p class="text-sm font-bold text-slate-900">
                            Aktifkan promo
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Promo akan aktif sesuai masa berlaku yang telah ditentukan.
                        </p>

                    </div>

                </label>

                @error('status')

                    <p class="mt-1.5 text-xs font-medium text-rose-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>

        </div>


        {{-- Form Actions --}}
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

            <a
                href="{{ route('super_admin.subscription_promotions.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-6 py-3 text-sm font-extrabold text-slate-700 transition hover:bg-slate-200"
            >
                Batal
            </a>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
            >
                <i class="fa-solid fa-save"></i>
                Simpan Promo
            </button>

        </div>

    </form>

</div>

@endsection


@push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const discountTypes = document.querySelectorAll(
            'input[name="discount_type"]'
        );

        const discountUnit = document.getElementById('discount-unit');
        const discountHelp = document.getElementById('discount-help');
        const discountValue = document.getElementById('discount_value');


        function updateDiscountType() {

            const selected = document.querySelector(
                'input[name="discount_type"]:checked'
            );

            if (!selected) {
                return;
            }


            if (selected.value === 'percentage') {

                discountUnit.textContent = '%';

                discountHelp.textContent =
                    'Masukkan persentase diskon antara 0 sampai 100%.';

                discountValue.max = '100';

            } else {

                discountUnit.textContent = 'Rp';

                discountHelp.textContent =
                    'Masukkan nominal potongan harga dalam Rupiah.';

                discountValue.removeAttribute('max');

            }

        }


        discountTypes.forEach(function (radio) {

            radio.addEventListener(
                'change',
                updateDiscountType
            );

        });


        updateDiscountType();


        // Highlight selected duration
        document.querySelectorAll(
            '.duration-option input[type="checkbox"]'
        ).forEach(function (checkbox) {

            function updateDurationStyle() {

                const label = checkbox.closest(
                    '.duration-option'
                );

                if (!label) {
                    return;
                }

                if (checkbox.checked) {

                    label.classList.add(
                        'border-amber-400',
                        'bg-amber-50'
                    );

                    label.classList.remove(
                        'border-slate-200'
                    );

                } else {

                    label.classList.remove(
                        'border-amber-400',
                        'bg-amber-50'
                    );

                    label.classList.add(
                        'border-slate-200'
                    );

                }

            }


            checkbox.addEventListener(
                'change',
                updateDurationStyle
            );


            updateDurationStyle();

        });

    });
</script>

@endpush
