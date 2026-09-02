@extends('layouts.customer')

@section('disableSweetAlert')
@endsection

@section('content')

    <div class="min-h-screen bg-slate-50">

        {{-- ==========================================================
            HEADER
        =========================================================== --}}
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">

            <div class="mx-auto max-w-5xl px-4 sm:px-6">

                <div class="flex items-center justify-between gap-4 py-4">

                    {{-- Merchant Info --}}
                    <div class="min-w-0">

                        <h1 class="truncate text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                            {{ $merchant->name ?? 'PesanIn' }}
                        </h1>

                        <p class="mt-1 truncate text-xs text-slate-500 sm:text-sm">
                            <i class="fa-solid fa-location-dot mr-1 text-[11px] text-amber-500"></i>
                            {{ $qrCode->name }}
                        </p>

                    </div>


                    {{-- Cart --}}
                    <a
                        href="{{ route('customer.cart', $qrCode->code) }}"
                        class="relative inline-flex shrink-0 items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-extrabold text-slate-950 shadow-sm transition hover:bg-amber-400 active:scale-95"
                    >
                        <span class="relative flex items-center justify-center">

                            <i class="fa-solid fa-cart-shopping text-sm"></i>

                            {{-- Cart Count --}}
                            <span
                                id="cartCount"
                                class="absolute -right-2.5 -top-3 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-black leading-none text-white shadow-sm ring-2 ring-white"
                                style="{{ array_sum(session('cart', [])) > 0 ? '' : 'display: none;' }}"
                            >
                                {{ array_sum(session('cart', [])) }}
                            </span>

                        </span>

                    </a>

                </div>

            </div>

        </header>


        {{-- ==========================================================
            MAIN
        =========================================================== --}}
        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 sm:py-10">


            {{-- ======================================================
                FLASH MESSAGE
            ======================================================= --}}
            @if (session('success'))

                <div
                    id="successNotification"
                    class="fixed left-1/2 top-5 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2"
                >

                    <div
                        class="flex items-center gap-3 rounded-2xl border border-emerald-200/70 bg-emerald-50/90 px-4 py-3.5 shadow-lg shadow-emerald-900/10 backdrop-blur-md"
                    >

                        {{-- Icon --}}
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100/90 text-emerald-600"
                        >
                            <i class="fa-solid fa-check text-sm"></i>
                        </div>


                        {{-- Message --}}
                        <div class="min-w-0 flex-1">

                            <p class="text-sm font-extrabold text-emerald-800">
                                Berhasil
                            </p>

                            <p class="mt-0.5 text-xs leading-5 text-emerald-700">
                                {{ session('success') }}
                            </p>

                        </div>


                        {{-- Close --}}
                        <button
                            type="button"
                            onclick="closeSuccessNotification()"
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-emerald-500 transition hover:bg-emerald-100 hover:text-emerald-700"
                            aria-label="Tutup notifikasi"
                        >
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>

                    </div>

                </div>

            @endif


            @if (session('error'))

                <div class="mb-8 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-4">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">

                        <i class="fa-solid fa-exclamation text-sm"></i>

                    </div>

                    <div class="pt-0.5">

                        <p class="text-sm font-extrabold text-red-800">
                            Tidak dapat menambahkan menu
                        </p>

                        <p class="mt-0.5 text-xs leading-5 text-red-700">
                            {{ session('error') }}
                        </p>

                    </div>

                </div>

            @endif


            {{-- ======================================================
                HERO / INTRO
            ======================================================= --}}
            <section class="pb-8 pt-4 text-center sm:pb-10">

                <span
                    class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-[10px] font-extrabold uppercase tracking-widest text-amber-600 sm:text-xs"
                >
                    Menu
                </span>

                <h2 class="mx-auto mt-4 max-w-2xl text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                    Mau pesan apa hari ini?
                </h2>

                <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500">
                    Pilih menu favoritmu dan tambahkan ke keranjang.
                </p>

            </section>


            {{-- ======================================================
                CATEGORY NAVIGATION
            ======================================================= --}}
            @if ($categories->count() > 0)

                <div class="mb-8 overflow-x-auto pb-1">

                    <div class="flex min-w-max items-center justify-center gap-2">

                        @foreach ($categories as $category)

                            <a
                                href="#category-{{ $category->id }}"
                                class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600"
                            >
                                {{ $category->name }}
                            </a>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- ======================================================
                CATEGORIES
            ======================================================= --}}
            @forelse ($categories as $category)

                <section
                    id="category-{{ $category->id }}"
                    class="mb-10 scroll-mt-28"
                >

                    {{-- Category Header --}}
                    <div class="mb-5 flex items-center gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                            <i class="fa-solid fa-utensils text-sm"></i>

                        </div>

                        <div>

                            <h2 class="text-lg font-black text-slate-900 sm:text-xl">
                                {{ $category->name }}
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $category->menus->count() }} menu
                            </p>

                        </div>

                    </div>


                    {{-- Menu Grid --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

                        @forelse ($category->menus as $menu)

                            <article
                                class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                            >

                                {{-- ==================================================
                                    IMAGE
                                =================================================== --}}
                                <div class="relative h-48 overflow-hidden bg-slate-100 sm:h-52">

                                    <img
                                        src="{{ menuImage($menu->image_path ?? $menu->image) }}"
                                        alt="{{ $menu->name }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    >

                                    {{-- Status Badge --}}
                                    @if ($menu->status == 'available')

                                        <div class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-[10px] font-extrabold text-emerald-600 shadow-sm backdrop-blur">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Tersedia

                                        </div>

                                    @else

                                        <div class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-[10px] font-extrabold text-red-500 shadow-sm backdrop-blur">

                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                            Habis

                                        </div>

                                    @endif

                                </div>


                                {{-- ==================================================
                                    CONTENT
                                =================================================== --}}
                                <div class="flex flex-1 flex-col p-5">

                                    {{-- Menu Name --}}
                                    <h3 class="text-base font-black leading-snug text-slate-900 sm:text-lg">
                                        {{ $menu->name }}
                                    </h3>


                                    {{-- Description --}}
                                    @if ($menu->description)

                                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500 sm:text-sm">
                                            {{ strip_tags($menu->description ?? '') }}
                                        </p>

                                    @else

                                        <p class="mt-2 text-xs italic text-slate-400 sm:text-sm">
                                            Tidak ada deskripsi.
                                        </p>

                                    @endif


                                    {{-- Price + Button --}}
                                    <div class="mt-auto flex items-end justify-between gap-3 pt-6">

                                        <div>

                                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                                Harga
                                            </p>

                                            <p class="mt-0.5 text-base font-black text-slate-900 sm:text-lg">
                                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                                            </p>

                                        </div>


                                        {{-- Add Cart / Habis Button --}}
                                        @if ($menu->status == 'available')
                                            <form action="{{ route('customer.cart.add', $qrCode->code) }}" method="POST" class="add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="add-to-cart-btn inline-flex items-center gap-2 rounded-xl bg-amber-500 px-3.5 py-2.5 text-xs font-extrabold text-slate-950 shadow-sm transition hover:bg-amber-400 active:scale-95 sm:text-sm">
                                                    <i class="fa-solid fa-plus text-[10px]"></i> Tambah
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl bg-slate-100 px-3.5 py-2.5 text-xs font-extrabold text-slate-400 sm:text-sm">
                                                <i class="fa-solid fa-ban text-[10px]"></i> Habis
                                            </button>
                                        @endif

                                    </div>

                                </div>

                            </article>

                        @empty

                            <div class="sm:col-span-2 lg:col-span-3">

                                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">

                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                        <i class="fa-solid fa-utensils text-xl"></i>

                                    </div>

                                    <h3 class="mt-4 text-sm font-extrabold text-slate-800">
                                        Belum ada menu
                                    </h3>

                                    <p class="mx-auto mt-1 max-w-sm text-xs leading-5 text-slate-500">
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
                <div class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center shadow-sm">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">

                        <i class="fa-solid fa-utensils text-2xl"></i>

                    </div>

                    <h2 class="mt-5 text-lg font-black text-slate-900">
                        Menu belum tersedia
                    </h2>

                    <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                        Saat ini belum ada kategori atau menu yang tersedia.
                    </p>

                </div>

            @endforelse


            {{-- ======================================================
                FOOTER
            ======================================================= --}}
            <footer class="border-t border-slate-200 pt-6 pb-4 text-center">

                <p class="text-xs text-slate-400">
                    © {{ date('Y') }} PesanIn. Semua hak dilindungi.
                </p>
            </footer>

        </main>

    </div>

@endsection

@push('scripts')
    @vite('resources/js/customer/menu.js')
@endpush

@push('styles')
    @vite('resources/css/customer/menu.css')
@endpush