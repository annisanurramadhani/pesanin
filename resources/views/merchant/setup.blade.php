@extends('layouts.app')

@section('body')
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush


    <div class="min-h-screen bg-slate-50">

        {{-- Header --}}
        <header class="border-b border-slate-200 bg-white">

            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">

                <div>

                    <h1 class="text-xl font-extrabold text-slate-900">
                        PesanIn
                    </h1>

                    <p class="text-xs text-slate-500">
                        Solusi digital untuk bisnis Anda
                    </p>

                </div>


                <div class="flex items-center gap-2 text-sm font-bold text-slate-500">

                    <span class="hidden sm:inline">
                        Langkah
                    </span>

                    <span
                        class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-xs font-black text-slate-950">
                        1
                    </span>

                    <span class="hidden sm:inline">
                        dari 2
                    </span>

                </div>

            </div>

        </header>


        {{-- Main Content --}}
        <main class="px-6 py-14">

            <div class="mx-auto max-w-3xl">

                {{-- Heading --}}
                <div class="mb-10 text-center">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">

                        Data Toko

                    </span>


                    <h2 class="mt-5 text-3xl font-black tracking-tight text-slate-900 md:text-4xl">

                        Lengkapi Data Toko Anda

                    </h2>


                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500">

                        Masukkan informasi dasar toko untuk melanjutkan proses
                        berlangganan PesanIn.

                    </p>

                </div>


                {{-- Form Card --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    {{-- Card Header --}}
                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                        <div class="flex items-center gap-4">

                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-store text-lg"></i>

                            </div>


                            <div>

                                <h3 class="font-extrabold text-slate-900">
                                    Informasi Toko
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    Informasi ini digunakan untuk membuat profil toko Anda.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Form --}}
                    <form method="POST" action="{{ route('merchant.setup.store') }}" class="space-y-6 p-6 sm:p-8">

                        @csrf


                        {{-- Nama Toko --}}
                        <div>

                            <label for="name"
                                class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                Nama Toko

                                <span class="text-red-500">*</span>

                            </label>


                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="Contoh: Warkop" autocomplete="organization" required autofocus
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">


                            @error('name')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-500">

                                    <i class="fa-solid fa-circle-exclamation"></i>

                                    {{ $message }}

                                </p>
                            @enderror

                        </div>


                        {{-- Nomor HP --}}
                        <div>

                            <label for="phone"
                                class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                Nomor HP / WhatsApp

                            </label>


                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                                inputmode="numeric" pattern="[0-9]*" maxlength="15" placeholder="Contoh: 081234567890"
                                class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-6 py-3.5 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">


                            @error('phone')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-500">

                                    <i class="fa-solid fa-circle-exclamation"></i>

                                    {{ $message }}

                                </p>
                            @enderror

                        </div>


                        {{-- Alamat --}}
                        <div>

                            <label for="address"
                                class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                Alamat Toko

                            </label>


                            <textarea id="address" name="address" rows="4" placeholder="Masukkan alamat lengkap toko"
                                autocomplete="street-address"
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">{{ old('address') }}</textarea>


                            @error('address')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-500">

                                    <i class="fa-solid fa-circle-exclamation"></i>

                                    {{ $message }}

                                </p>
                            @enderror

                        </div>


                        {{-- Lokasi Toko --}}
                        <div>

                            <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                Lokasi Toko

                                <span class="text-red-500">*</span>

                            </label>


                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                                {{-- Location Header --}}
                                <div class="mb-4 flex items-start gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                        <i class="fa-solid fa-map-location-dot"></i>

                                    </div>


                                    <div>

                                        <p class="text-sm font-extrabold text-slate-800">
                                            Tentukan lokasi toko
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Klik pada peta atau geser pin untuk menentukan
                                            posisi toko Anda.
                                        </p>

                                    </div>

                                </div>

                                {{-- Search Location --}}
                                <div class="mb-3 flex gap-2">

                                    <input type="text" id="mapSearch" placeholder="Cari alamat atau nama tempat..."
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10">

                                    <button type="button" id="searchLocation"
                                        class="shrink-0 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-amber-400">

                                        <i class="fa-solid fa-magnifying-glass"></i>

                                    </button>

                                </div>

                                {{-- Map --}}
                                <div id="merchantMap"
                                    class="h-80 w-full overflow-hidden rounded-xl border border-slate-200">
                                </div>


                                {{-- Location Status --}}
                                <div class="mt-3 rounded-xl border border-slate-200 bg-white p-3">

                                    <div class="flex items-center gap-2">

                                        <i class="fa-solid fa-location-dot text-amber-500"></i>

                                        <div>

                                            <p class="text-xs font-bold text-slate-700">
                                                Titik lokasi toko
                                            </p>

                                            <p id="locationStatus" class="mt-0.5 text-xs text-slate-400">

                                                Silakan tentukan titik lokasi toko pada peta.

                                            </p>

                                        </div>

                                    </div>

                                </div>


                                {{-- Device Location --}}
                                <button type="button" id="useDeviceLocation"
                                    class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm font-extrabold text-amber-600 transition hover:bg-amber-50">

                                    <i class="fa-solid fa-location-crosshairs"></i>

                                    Gunakan Lokasi Perangkat

                                </button>


                                {{-- Coordinates --}}
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">


                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                            </div>


                            @error('latitude')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-500">

                                    <i class="fa-solid fa-circle-exclamation"></i>

                                    {{ $message }}

                                </p>
                            @enderror


                            @error('longitude')
                                <p class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-red-500">

                                    <i class="fa-solid fa-circle-exclamation"></i>

                                    {{ $message }}

                                </p>
                            @enderror

                        </div>


                        {{-- Information --}}
                        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-amber-500 shadow-sm">

                                    <i class="fa-solid fa-info text-sm"></i>

                                </div>


                                <div>

                                    <p class="text-xs font-extrabold text-slate-800">
                                        Informasi Langganan
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-600">

                                        Setelah data toko disimpan, langganan akan dibuat
                                        dengan status

                                        <span class="font-extrabold text-amber-600">
                                            Pending
                                        </span>.

                                        Langganan akan menjadi

                                        <span class="font-extrabold text-emerald-600">
                                            Active
                                        </span>

                                        setelah pembayaran berhasil.

                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Buttons --}}
                        <div class="flex flex-col gap-3 border-t border-slate-100 pt-6 sm:flex-row">

                            <a href="{{ route('public.subscription.index') }}"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-100 px-5 py-3.5 text-sm font-extrabold text-slate-700 transition hover:bg-slate-200 sm:w-1/3">

                                <i class="fa-solid fa-arrow-left"></i>

                                Kembali

                            </a>


                            <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400 sm:w-2/3">

                                <span>
                                    Simpan & Lanjutkan
                                </span>

                                <i class="fa-solid fa-arrow-right"></i>

                            </button>

                        </div>

                    </form>

                </div>


                {{-- Security Note --}}
                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-400">

                    <i class="fa-solid fa-lock"></i>

                    <span>
                        Data toko Anda disimpan dengan aman.
                    </span>

                </div>

            </div>

        </main>

    </div>


    {{-- Leaflet JS --}}
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="{{ asset('js/merchant/merchant-setup.js') }}"></script>
    @endpush
@endsection
