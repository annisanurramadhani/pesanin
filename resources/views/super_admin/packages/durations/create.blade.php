@extends('layouts.admin')

@section('header')

<div class="flex items-center gap-4">

    <a
        href="{{ route('super_admin.packages.durations.index', [
            'encryptedId' => encryptId($package->id),
        ]) }}"
        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800"
        title="Kembali"
    >
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Tambah Durasi Paket
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Tambahkan pilihan durasi dan harga untuk paket
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

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-500">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>

                <div>

                    <p class="text-sm font-bold text-rose-700">
                        Silakan perbaiki kesalahan berikut:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-xs font-medium text-rose-600">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Card Header --}}
        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>

                    <h2 class="font-extrabold text-slate-900">
                        Informasi Durasi
                    </h2>

                    <p class="text-xs text-slate-400">
                        Lengkapi informasi durasi, harga, dan status paket.
                    </p>

                </div>

            </div>

        </div>


        {{-- Form --}}
        <form
            action="{{ route('super_admin.packages.durations.store', encryptId($package->id)) }}"
            method="POST"
            class="duration-form p-6"
        >

            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                {{-- Duration Name --}}
                <div>

                    <label
                        for="name"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Nama Durasi
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Monthly"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('name') border-rose-400 @enderror"
                    >

                    <p class="mt-1.5 text-xs text-slate-400">
                        Contoh: Monthly, Quarterly, Yearly.
                    </p>

                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Duration Days --}}
                <div>

                    <label
                        for="duration_days"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Durasi
                        <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">

                        <input
                            type="number"
                            id="duration_days"
                            name="duration_days"
                            value="{{ old('duration_days') }}"
                            min="1"
                            placeholder="30"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-16 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('duration_days') border-rose-400 @enderror"
                        >

                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">
                            hari
                        </span>

                    </div>

                    @error('duration_days')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Price --}}
                <div>

                    <label
                        for="price"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Harga Normal
                        <span class="text-rose-500">*</span>
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">
                            Rp
                        </span>

                        <input
                            type="number"
                            id="price"
                            name="price"
                            value="{{ old('price') }}"
                            min="0"
                            step="0.01"
                            placeholder="50000"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('price') border-rose-400 @enderror"
                        >

                    </div>

                    <p class="mt-1.5 text-xs text-slate-400">
                        Harga normal sebelum diskon.
                    </p>

                    @error('price')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Discount Price --}}
                <div>

                    <label
                        for="discount_price"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Harga Diskon
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">
                            Rp
                        </span>

                        <input
                            type="number"
                            id="discount_price"
                            name="discount_price"
                            value="{{ old('discount_price') }}"
                            min="0"
                            step="0.01"
                            placeholder="40000"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('discount_price') border-rose-400 @enderror"
                        >

                    </div>

                    <p class="mt-1.5 text-xs text-slate-400">
                        Kosongkan jika tidak ada harga diskon.
                    </p>

                    @error('discount_price')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Status --}}
                <div>

                    <label
                        for="status"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Status
                        <span class="text-rose-500">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('status') border-rose-400 @enderror"
                    >

                        <option
                            value="active"
                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            {{ old('status') === 'inactive' ? 'selected' : '' }}
                        >
                            Nonaktif
                        </option>

                    </select>

                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- Package Info --}}
            <div class="mt-6">

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                    <div class="flex items-start gap-4">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i class="fa-solid fa-box"></i>
                        </div>

                        <div>

                            <p class="text-sm font-extrabold text-slate-800">
                                Paket
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Durasi ini akan ditambahkan ke paket
                                <span class="font-bold text-slate-700">
                                    {{ strip_tags($package->name) }}
                                </span>.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Form Actions --}}
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                <a
                    href="{{ route('super_admin.packages.durations.index', encryptId($package->id)) }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-clock"></i>
                    Simpan Durasi
                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')

<script src="{{ asset('js/super_admin/duration.js') }}"></script>

@endpush
