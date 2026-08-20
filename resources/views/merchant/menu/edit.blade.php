@extends('layouts.app')

@section('body')

    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                            Edit Detail Menu
                        </h2>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Ubah informasi produk, harga, stok, kategori, atau foto menu.
                        </p>
                    </div>

                    <a href="{{ route('merchant.menu.index') }}"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-xl text-xs transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-8">
                <div class="mx-auto max-w-2xl">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">

                        <form action="{{ route('merchant.menu.update', encryptId($menu->id)) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="space-y-5">

                            @csrf
                            @method('PUT')

                            {{-- Kategori --}}
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Pilih Kategori *
                                </label>
                                <select name="category_id"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition"
                                    required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $menu->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Nama Menu --}}
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Nama Menu *
                                </label>
                                <input type="text"
                                    name="name"
                                    value="{{ old('name', $menu->name) }}"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition"
                                    required>
                            </div>

                            {{-- Harga & Stok --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                        Harga (Rp) *
                                    </label>
                                    <input type="number"
                                        name="price"
                                        value="{{ old('price', $menu->price) }}"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                        Stok (Porsi) *
                                    </label>
                                    <input type="number"
                                        name="stock"
                                        value="{{ old('stock', $menu->stock ?? 0) }}"
                                        min="0"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition"
                                        required>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Deskripsi (Opsional)
                                </label>
                                <textarea name="description"
                                    rows="3"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition">{{ old('description', $menu->description) }}</textarea>
                            </div>

                            {{-- Foto --}}
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Foto Saat Ini
                                </label>
                                <div class="mb-3">
                                    <img src="{{ menuImage($menu->image_path ?? $menu->image) }}"
                                        alt="{{ $menu->name }}"
                                        class="w-24 h-24 rounded-2xl object-cover border border-slate-200 shadow-sm">
                                </div>
                                        
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Ganti Foto
                                </label>
                                <input type="file"
                                    name="image"
                                    accept="image/*"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl text-xs p-2.5 text-slate-700 transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                            </div>

                            {{-- Tombol --}}
                            <div class="pt-4 flex flex-col sm:flex-row gap-3 border-t border-slate-100">
                                <a href="{{ route('merchant.menu.index') }}"
                                    class="w-full sm:w-1/2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold py-3.5 rounded-xl text-sm transition">
                                    Batal
                                </a>

                                <button type="submit"
                                    class="w-full sm:w-1/2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3.5 rounded-xl text-sm shadow-lg shadow-amber-500/25 transition">
                                    <i class="fa-solid fa-floppy-disk mr-1"></i>
                                    Simpan Perubahan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>

@endsection