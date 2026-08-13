@extends('layouts.admin')

@section('header')

<div class="flex items-center gap-4">

    <a
        href="{{ route('super_admin.packages.index') }}"
        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800"
        title="Kembali"
    >
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Edit Paket
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Perbarui informasi paket berlangganan.
        </p>

    </div>

</div>

@endsection


@section('content')

<div class="mx-auto max-w-5xl">

    {{-- Success Message --}}
    @if (session('success'))

        <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-600">

            <i class="fa-solid fa-circle-check"></i>

            <span class="text-sm font-semibold">
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- Validation Errors --}}
    @if ($errors->any())

        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4">

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


    {{-- Package Information --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Section Header --}}
        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center justify-between gap-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                        <i class="fa-solid fa-box"></i>
                    </div>

                    <div>

                        <h2 class="font-extrabold text-slate-900">
                            Informasi Paket
                        </h2>

                        <p class="text-xs text-slate-400">
                            Perbarui informasi paket dengan benar.
                        </p>

                    </div>

                </div>


                {{-- Package Status --}}
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

            </div>

        </div>


        <form
            action="{{ route('super_admin.packages.update', encryptId($package->id)) }}"
            method="POST"
            class="p-6 package-form"
        >

            @csrf

            @method('PUT')


            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                {{-- Package Name --}}
                <div class="md:col-span-2">

                    <label
                        for="name"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Nama Paket
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', strip_tags($package->name)) }}"
                        maxlength="100"
                        required
                        autofocus
                        placeholder="Contoh: Paket Premium"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('name') border-rose-500 @enderror"
                    >

                    @error('name')

                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Badge --}}
                <div class="md:col-span-2">

                    <label
                        for="badge"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Badge
                    </label>

                    <input
                        type="text"
                        id="badge"
                        name="badge"
                        value="{{ old('badge', strip_tags($package->badge)) }}"
                        maxlength="50"
                        placeholder="Contoh: Popular, Recommended"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('badge') border-rose-500 @enderror"
                    >

                    <p class="mt-1.5 text-xs text-slate-400">
                        Label yang akan ditampilkan sebagai penanda paket.
                    </p>

                    @error('badge')

                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Description --}}
                <div class="md:col-span-2">

                    <label
                        for="description"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        placeholder="Jelaskan apa saja yang ditawarkan paket ini..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('description') border-rose-500 @enderror"
                    >{{ old('description', strip_tags($package->description)) }}</textarea>

                    @error('description')

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
                        name="status"
                        id="status"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('status') border-rose-500 @enderror"
                    >

                        <option
                            value="active"
                            {{ old('status', $package->status) === 'active' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            {{ old('status', $package->status) === 'inactive' ? 'selected' : '' }}
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


                {{-- Sort Order --}}
                <div>

                    <label
                        for="sort_order"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Urutan
                        <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="number"
                        name="sort_order"
                        id="sort_order"
                        value="{{ old('sort_order', $package->sort_order) }}"
                        min="0"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('sort_order') border-rose-500 @enderror"
                    >

                    <p class="mt-1.5 text-xs text-slate-400">
                        Nomor yang lebih kecil akan ditampilkan lebih dahulu.
                    </p>

                    @error('sort_order')

                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Slug Information --}}
                <div class="md:col-span-2">

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                        <div class="flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-link"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-extrabold text-slate-800">
                                    Informasi Slug
                                </p>

                                <div class="mt-2 flex flex-wrap items-center gap-2">

                                    <span class="text-xs font-semibold text-slate-500">
                                        Slug saat ini:
                                    </span>

                                    <code class="rounded-lg bg-white px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        {{ $package->slug }}
                                    </code>

                                </div>

                                <p class="mt-2 text-xs leading-5 text-slate-500">
                                    Slug akan dibuat ulang secara otomatis ketika nama paket berubah.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Actions --}}
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                <a
                    href="{{ route('super_admin.packages.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-pen-to-square"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>


    {{-- Package Summary --}}
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>

                <div>

                    <h2 class="font-extrabold text-slate-900">
                        Ringkasan Paket
                    </h2>

                    <p class="text-xs text-slate-400">
                        Informasi singkat mengenai paket ini.
                    </p>

                </div>

            </div>

        </div>


        <div class="p-6">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                {{-- Package --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-xs font-semibold text-slate-400">
                        Paket
                    </p>

                    <p class="mt-1 text-lg font-extrabold text-slate-800">
                        {{ strip_tags($package->name) }}
                    </p>

                </div>


                {{-- Durations --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-xs font-semibold text-slate-400">
                        Durasi
                    </p>

                    <p class="mt-1 text-lg font-extrabold text-slate-800">
                        {{ $package->durations->count() }}
                    </p>

                </div>


                {{-- Status --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                    <p class="text-xs font-semibold text-slate-400">
                        Status
                    </p>

                    @if ($package->status === 'active')

                        <p class="mt-1 text-lg font-extrabold text-emerald-600">
                            Aktif
                        </p>

                    @else

                        <p class="mt-1 text-lg font-extrabold text-rose-500">
                            Nonaktif
                        </p>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('js/super_admin/package.js') }}"></script>

@endpush
