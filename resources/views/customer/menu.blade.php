@extends('layouts.customer')

@section('content')

    <div class="min-h-screen bg-[#F3F4F8]">

        {{-- ==========================================================
            HEADER
        =========================================================== --}}
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20">

            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4">

                <div class="flex items-center justify-between gap-4">

                    {{-- Merchant Info --}}
                    <div class="min-w-0">

                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">
                            {{ $merchant->name ?? 'PesanIn' }}
                        </h1>

                        <div class="flex items-center gap-2 mt-1">

                            <i class="fa-solid fa-location-dot text-xs text-slate-400"></i>

                            <p class="text-xs sm:text-sm text-slate-500 truncate">
                                {{ $qrCode->name }}
                            </p>

                        </div>

                    </div>


                    {{-- Cart --}}
                    <a
                        href="{{ route('customer.cart', $qrCode->code) }}"
                        class="relative flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm"
                    >

                        <i class="fa-solid fa-cart-shopping text-sm"></i>

                        <span class="hidden sm:inline">
                            Keranjang
                        </span>

                    </a>

                </div>

            </div>

        </header>


        {{-- ==========================================================
            MAIN
        =========================================================== --}}
        <main class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">


            {{-- ======================================================
                FLASH MESSAGE
            ======================================================= --}}
            @if (session('success'))

                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5">

                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-100">
                        <i class="fa-solid fa-check text-sm text-emerald-600"></i>
                    </div>

                    <div class="pt-0.5">
                        <p class="text-sm font-semibold text-emerald-800">
                            Berhasil
                        </p>

                        <p class="text-xs text-emerald-700 mt-0.5">
                            {{ session('success') }}
                        </p>
                    </div>

                </div>

            @endif


            @if (session('error'))

                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5">

                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-red-100">
                        <i class="fa-solid fa-exclamation text-sm text-red-600"></i>
                    </div>

                    <div class="pt-0.5">
                        <p class="text-sm font-semibold text-red-800">
                            Tidak dapat menambahkan menu
                        </p>

                        <p class="text-xs text-red-700 mt-0.5">
                            {{ session('error') }}
                        </p>
                    </div>

                </div>

            @endif


            {{-- ======================================================
                INTRO
            ======================================================= --}}
            <div class="mb-8">

                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    Menu
                </p>

                <h2 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Mau pesan apa hari ini?
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Pilih menu favoritmu dan tambahkan ke keranjang.
                </p>

            </div>


            {{-- ======================================================
                CATEGORIES
            ======================================================= --}}
            @forelse ($categories as $category)

                <section class="mb-10">

                    {{-- Category Header --}}
                    <div class="flex items-center gap-3 mb-4">

                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                            <i class="fa-solid fa-utensils text-sm"></i>
                        </div>

                        <div>

                            <h2 class="text-lg sm:text-xl font-bold text-slate-900">
                                {{ $category->name }}
                            </h2>

                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $category->menus->count() }} menu
                            </p>

                        </div>

                    </div>


                    {{-- Menu Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

                        @forelse ($category->menus as $menu)

                            <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md hover:border-slate-300 transition duration-200">


                                {{-- ==================================================
                                    IMAGE
                                =================================================== --}}
                                <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100">

                                    @if ($menu->image)

                                        <img
                                            src="{{ asset('storage/' . $menu->image) }}"
                                            alt="{{ $menu->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                        >

                                    @else

                                        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100">

                                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center shadow-sm">

                                                <i class="fa-solid fa-utensils text-xl text-slate-300"></i>

                                            </div>

                                            <span class="text-xs text-slate-400 mt-2">
                                                Tidak ada gambar
                                            </span>

                                        </div>

                                    @endif


                                    {{-- Stock Badge --}}
                                    @if ($menu->stock > 0)

                                        <div class="absolute top-3 right-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 backdrop-blur px-2.5 py-1 text-[10px] font-bold text-emerald-600 shadow-sm">

                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                                            Tersedia

                                        </div>

                                    @else

                                        <div class="absolute top-3 right-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 backdrop-blur px-2.5 py-1 text-[10px] font-bold text-red-500 shadow-sm">

                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>

                                            Habis

                                        </div>

                                    @endif

                                </div>


                                {{-- ==================================================
                                    CONTENT
                                =================================================== --}}
                                <div class="p-4 sm:p-5">

                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg leading-snug">
                                        {{ $menu->name }}
                                    </h3>


                                    @if ($menu->description)

                                        <p class="text-xs sm:text-sm text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                            {{ $menu->description }}
                                        </p>

                                    @else

                                        <p class="text-xs sm:text-sm text-slate-400 mt-2 italic">
                                            Tidak ada deskripsi.
                                        </p>

                                    @endif


                                    {{-- Price + Add --}}
                                    <div class="flex items-end justify-between gap-3 mt-5">

                                        <div>

                                            <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">
                                                Harga
                                            </p>

                                            <p class="text-base sm:text-lg font-extrabold text-slate-900 mt-0.5">
                                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                                            </p>

                                        </div>


                                        {{-- Add Cart --}}
                                        @if ($menu->stock > 0)

                                            <form
                                                action="{{ route('customer.cart.add', $qrCode->code) }}"
                                                method="POST"
                                            >

                                                @csrf

                                                <input
                                                    type="hidden"
                                                    name="menu_id"
                                                    value="{{ $menu->id }}"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="quantity"
                                                    value="1"
                                                >

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-900 text-white text-xs sm:text-sm font-bold hover:bg-slate-800 active:scale-95 transition"
                                                >

                                                    <i class="fa-solid fa-plus text-[10px]"></i>

                                                    Tambah

                                                </button>

                                            </form>

                                        @else

                                            <button
                                                type="button"
                                                disabled
                                                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-400 text-xs sm:text-sm font-bold cursor-not-allowed"
                                            >

                                                <i class="fa-solid fa-ban text-[10px]"></i>

                                                Habis

                                            </button>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="sm:col-span-2 lg:col-span-3">

                                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">

                                    <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 flex items-center justify-center">

                                        <i class="fa-solid fa-utensils text-slate-400"></i>

                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-700">
                                        Belum ada menu
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Belum ada menu tersedia pada kategori ini.
                                    </p>

                                </div>

                            </div>

                        @endforelse

                    </div>

                </section>

            @empty

                {{-- ======================================================
                    NO CATEGORY
                ======================================================= --}}
                <div class="bg-white rounded-2xl border border-slate-200 px-6 py-14 text-center shadow-sm">

                    <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center">

                        <i class="fa-solid fa-utensils text-2xl text-slate-400"></i>

                    </div>

                    <h2 class="mt-4 text-lg font-bold text-slate-800">
                        Menu belum tersedia
                    </h2>

                    <p class="mt-2 text-sm text-slate-500 max-w-sm mx-auto">
                        Saat ini belum ada kategori atau menu yang tersedia.
                    </p>

                </div>

            @endforelse


            {{-- ======================================================
                FOOTER
            ======================================================= --}}
            <div class="pt-4 pb-8 text-center">

                <p class="text-[11px] text-slate-400">
                    Powered by
                    <span class="font-bold text-slate-500">
                        PesanIn
                    </span>
                </p>

            </div>

        </main>

    </div>

@endsection