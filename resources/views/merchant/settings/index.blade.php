@extends('layouts.merchant')


{{-- =========================================================
    HEADER
========================================================= --}}
@section('header')

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>

            <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                Pengaturan
            </h2>

            <p class="text-xs font-medium text-slate-500 mt-1">
                Kelola informasi bisnis Anda.
            </p>

        </div>

    </div>

@endsection


{{-- =========================================================
    CONTENT
========================================================= --}}
@section('content')

    <div class="space-y-8">

        {{-- =====================================================
            INFORMASI BISNIS
        ====================================================== --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">

            {{-- Card Header --}}
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">

                <div>

                    <h3 class="font-extrabold text-slate-900 text-base">
                        Informasi Bisnis
                    </h3>

                    <p class="text-[11px] font-medium text-slate-400">
                        Kelola informasi bisnis yang digunakan pada sistem PesanIn.
                    </p>

                </div>

            </div>


            {{-- =================================================
                FORM
            ================================================== --}}
            <form
                action="{{ route('merchant.settings.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-5"
            >

                @csrf

                @method('PUT')


                {{-- =================================================
                    NAMA BISNIS
                ================================================== --}}
                <div>

                    <label
                        for="name"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Nama Bisnis
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $merchant->name) }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        placeholder="Masukkan nama bisnis"
                        required
                    >

                    @error('name')
                        <p class="mt-2 text-xs font-semibold text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- =================================================
                    LOGO BISNIS
                ================================================== --}}
                <div>

                    <label
                        for="logo"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Logo Bisnis
                    </label>


                    {{-- Logo Saat Ini --}}
                    {{-- Preview Logo --}}
                    <div class="mb-4 flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">

                        <img
                            src="{{ $merchant->logo
                                ? asset('storage/' . $merchant->logo)
                                : asset('assets/images/menu-default.jpg') }}"
                            alt="{{ $merchant->logo ? 'Logo ' . $merchant->name : 'Logo default PesanIn' }}"
                            class="h-16 w-16 rounded-xl border border-slate-200 bg-white object-cover"
                        >

                        <div>

                            @if ($merchant->logo)

                                <p class="text-sm font-bold text-slate-800">
                                    Logo saat ini
                                </p>

                                <p class="mt-1 text-[11px] font-medium text-slate-400">
                                    Pilih logo baru jika ingin menggantinya.
                                </p>

                            @else

                                <p class="text-sm font-bold text-slate-800">
                                    Logo default
                                </p>

                                <p class="mt-1 text-[11px] font-medium text-slate-400">
                                    Belum ada logo bisnis. Pilih file baru untuk menggantinya.
                                </p>

                            @endif

                        </div>

                    </div>


                    {{-- File Input --}}
                    <input
                        id="logo"
                        type="file"
                        name="logo"
                        accept="image/png,image/jpeg,image/jpg,image/webp"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-amber-50 file:px-4 file:py-2 file:text-xs file:font-extrabold file:text-amber-600 hover:file:bg-amber-100"
                    >

                    <p class="mt-2 text-[11px] font-medium text-slate-400">
                        Format: JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                    </p>

                    @error('logo')
                        <p class="mt-2 text-xs font-semibold text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- =================================================
                    NOMOR TELEPON
                ================================================== --}}
                <div>

                    <label
                        for="phone"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Nomor Telepon
                    </label>

                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        value="{{ old('phone', $merchant->phone) }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        placeholder="Masukkan nomor telepon"
                    >

                    @error('phone')
                        <p class="mt-2 text-xs font-semibold text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- =================================================
                    ALAMAT
                ================================================== --}}
                <div>

                    <x-rich-text-editor
                        name="address"
                        label="Alamat"
                        :value="old('address', $merchant->address)"
                        placeholder="Masukkan alamat bisnis..."
                    />

                </div>


                {{-- =================================================
                    DESKRIPSI
                ================================================== --}}
                {{-- <div>

                    <label
                        for="description"
                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2"
                    >
                        Deskripsi Bisnis
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                        placeholder="Masukkan deskripsi bisnis"
                    >{{ old('description', optional($merchant->settings)->description) }}</textarea>

                    @error('description')
                        <p class="mt-2 text-xs font-semibold text-red-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div> --}}


                {{-- =================================================
                    BUTTON
                ================================================== --}}
                <div class="flex justify-end border-t border-slate-100 pt-5">

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-extrabold text-slate-950 shadow-md shadow-amber-500/20 transition hover:bg-amber-400"
                    >

                        <i class="fa-solid fa-floppy-disk text-xs"></i>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection