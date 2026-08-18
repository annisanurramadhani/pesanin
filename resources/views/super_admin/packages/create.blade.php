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
            Tambah Paket
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Buat paket berlangganan baru untuk pedagang.
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


    {{-- Package Card --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Section Header --}}
        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <i class="fa-solid fa-box"></i>
                </div>

                <div>

                    <h2 class="font-extrabold text-slate-900">
                        Informasi Paket
                    </h2>

                    <p class="text-xs text-slate-400">
                        Lengkapi informasi paket yang akan tersedia untuk pedagang.
                    </p>

                </div>

            </div>

        </div>


        <form
            action="{{ route('super_admin.packages.store') }}"
            method="POST"
            class="p-6 package-form"
        >

            @csrf


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
                        value="{{ old('name') }}"
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
                        value="{{ old('badge') }}"
                        maxlength="50"
                        placeholder="Contoh: Popular, Recommended"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('badge') border-rose-500 @enderror"
                    >

                    <p class="mt-1.5 text-xs text-slate-400">
                        Label opsional yang akan ditampilkan sebagai penanda paket.
                    </p>

                    @error('badge')

                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Description --}}
                <div class="md:col-span-2">


                    <x-rich-text-editor
    name="description"
    label="Deskripsi"
    placeholder="Jelaskan apa saja yang ditawarkan paket ini..."
    :value="old('description')"
/>

                    @error('description')

                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- Status --}}
                <div class="md:col-span-2">

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
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 @error('status') border-rose-500 @enderror"
                    >

                        <option
                            value="active"
                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            {{ old('status', 'active') === 'inactive' ? 'selected' : '' }}
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


                {{-- Package Information --}}
                <div class="md:col-span-2">

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                        <div class="flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div>

                                <p class="text-sm font-extrabold text-slate-800">
                                    Informasi Paket
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Setelah paket berhasil dibuat, Anda dapat menambahkan dan mengatur durasi paket melalui menu Kelola Durasi.
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
                    <i class="fa-solid fa-box"></i>
                    Simpan Paket
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script src="{{ asset('js/super_admin/package.js') }}"></script>

@endpush
