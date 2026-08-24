@extends('layouts.app')

@section('body')

    {{-- Layout dikunci seukuran layar monitor (h-screen & overflow-hidden) --}}
    <div class="h-screen flex overflow-hidden bg-slate-100">

        {{-- Sidebar mengunci di kiri layar --}}
        @include('components.sidebar.merchant')

        {{-- Area Konten Kanan (Satu-satunya area yang bisa di-scroll) --}}
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">

            {{-- Header Tetap Diam di Atas Konten (Sticky Header) --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-20 shadow-sm shrink-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                            Kelola Menu & Kategori
                        </h2>
                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Atur katalog produk, kategori makanan/minuman, dan ketersediaan stok tokomu.
                        </p>
                    </div>
                </div>
            </header>

            {{-- Isi Konten --}}
            <main class="flex-1 p-8">
                <div class="space-y-8">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pb-12">

                        {{-- Sidebar Forms --}}
                        <div class="lg:col-span-4 space-y-6">

                            {{-- Form Tambah Kategori --}}
                            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-500 flex items-center justify-center font-black text-base shadow-md">
                                        <i class="fa-solid fa-folder-plus"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-slate-900 text-base">Tambah Kategori</h3>
                                        <p class="text-[11px] font-medium text-slate-400">Misal: Coffee, Snack, Main Course</p>
                                    </div>
                                </div>

                                <form action="{{ route('merchant.category.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                            Nama Kategori *
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-300"
                                            placeholder="Contoh: Coffee & Espresso" required>
                                    </div>

                                    <button type="submit"
                                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-200 shadow-md active:scale-[0.98]">
                                        + Simpan Kategori
                                    </button>
                                </form>
                            </div>

                            {{-- Form Tambah Menu --}}
                            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
                                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-base shadow-md shadow-amber-500/20">
                                        <i class="fa-solid fa-mug-hot"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-slate-900 text-base">Tambah Menu Baru</h3>
                                        <p class="text-[11px] font-medium text-slate-400">Masukkan rincian item menu</p>
                                    </div>
                                </div>

                                <form action="{{ route('merchant.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf

                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                            Pilih Kategori *
                                        </label>
                                        <select name="category_id"
                                            class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition"
                                            required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                            Nama Menu *
                                        </label>
                                        <input type="text" name="name" value="{{ old('name') }}"
                                            class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-300"
                                            placeholder="Contoh: Ice Americano PST" required>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                                Harga (Rp) *
                                            </label>
                                            <input type="number" name="price" value="{{ old('price') }}"
                                                class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-300"
                                                placeholder="22000" required>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                                Stok (Porsi) *
                                            </label>
                                            <input type="number" name="stock" value="{{ old('stock') }}"
                                                class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-300"
                                                placeholder="0" min="0" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                            Foto Produk (Opsional)
                                        </label>
                                        <input type="file" name="image" accept="image/*"
                                            class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 rounded-xl text-xs p-2.5 text-slate-700 transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                            Deskripsi (Opsional)
                                        </label>
                                        <textarea name="description" rows="2"
                                            class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-300"
                                            placeholder="Keterangan singkat produk...">{{ old('description') }}</textarea>
                                    </div>

                                    <button type="submit"
                                        class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3.5 px-4 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-amber-500/25 active:scale-[0.98]">
                                        + Tambahkan Ke Katalog
                                    </button>
                                </form>
                            </div>

                        </div>

                        {{-- Main Catalogue --}}
                        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">

                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg">Katalog Menu Aktif</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Klik pada header kategori untuk membuka atau menutup daftar menu</p>
                                </div>
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-extrabold border border-amber-200/60">
                                    Total {{ count($menus) }} Produk
                                </span>
                            </div>

                            {{-- Pengelompokan Per Kategori --}}
                            @forelse($menus->groupBy(fn($item) => $item->category->name ?? 'Tanpa Kategori') as $categoryName => $groupedMenus)

                                <details class="group space-y-4 [&_summary::-webkit-details-marker]:hidden border border-slate-200/80 rounded-2xl p-4 bg-slate-50/30 transition">
                                    {{-- Header Judul Kategori --}}
                                    <summary class="flex items-center justify-between gap-3 cursor-pointer select-none py-1">
                                        <div class="flex items-center gap-3">
                                            <span class="px-3 py-1.5 bg-slate-900 text-amber-400 font-black rounded-xl text-xs uppercase tracking-wider shadow-sm flex items-center gap-2">
                                                <i class="fa-solid fa-layer-group text-[10px]"></i>
                                                {{ $categoryName }}
                                            </span>
                                            <span class="text-xs font-bold text-slate-400">
                                                ({{ count($groupedMenus) }} Item)
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <div class="h-[1px] w-16 sm:w-32 bg-slate-200"></div>
                                            <span class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-500 group-open:rotate-180 transition-transform duration-200 flex items-center justify-center text-xs shadow-sm">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </span>
                                        </div>
                                    </summary>

                                    {{-- Card Grid Isi Menu --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-200/60 mt-4">
                                        @foreach($groupedMenus as $menu)
                                            <div class="p-5 rounded-2xl border border-slate-200/70 bg-white hover:border-amber-500/50 hover:shadow-md transition-all duration-200 flex flex-col justify-between group/card">
                                                <div>
                                                    <div class="flex items-start justify-end gap-2 mb-3">
                                                        <div class="flex items-center gap-1">
                                                            <a href="{{ route('merchant.menu.edit', encryptId($menu->id)) }}"
                                                                class="w-7 h-7 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 flex items-center justify-center transition"
                                                                title="Edit Menu">
                                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                            </a>

                                                            {{-- Form Hapus --}}
                                                            <form action="{{ route('merchant.menu.destroy', encryptId($menu->id)) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="button"
                                                                    onclick="confirmDelete(event, this, '{{ addslashes($menu->name) }}')"
                                                                    class="w-7 h-7 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition"
                                                                    title="Hapus Menu">
                                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <div class="flex gap-3 mb-3">
                                                        <img src="{{ menuImage($menu->image_path ?? $menu->image) }}"
                                                        alt="{{ $menu->name }}"
                                                        class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">

                                                        <div>
                                                            <h4 class="font-extrabold text-slate-900 text-base group-hover/card:text-amber-600 transition leading-tight">
                                                                {{ $menu->name }}
                                                            </h4>
                                                            <p class="text-xs text-slate-500 font-medium line-clamp-2 mt-1">
                                                                {{ $menu->description ?? 'Tidak ada deskripsi.' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                                    <div>
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                                                            Harga Jual
                                                        </span>
                                                        <span class="font-bold text-slate-800">
                                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                                        </span>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-xl text-[10px] font-black border border-slate-200">
                                                            Stok: {{ $menu->stock ?? 0 }}
                                                        </span>

                                                        <form action="{{ route('merchant.menu.toggle', encryptId($menu->id)) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit"
                                                                class="px-2.5 py-1 rounded-xl text-[10px] font-black flex items-center gap-1.5 transition
                                                                {{ ($menu->is_available ?? true) && ($menu->stock > 0)
                                                                    ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200'
                                                                    : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}"
                                                                title="Klik untuk ubah status stok">
                                                                <span class="w-2 h-2 rounded-full
                                                                    {{ ($menu->is_available ?? true) && ($menu->stock > 0) ? 'bg-emerald-500' : 'bg-rose-500' }}">
                                                                </span>
                                                                <span>
                                                                    {{ ($menu->is_available ?? true) && ($menu->stock > 0) ? 'Ready' : 'Habis' }}
                                                                </span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>

                            @empty

                                <div class="p-12 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">
                                        <i class="fa-solid fa-utensils"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum ada menu yang dibuat</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan form di samping untuk mulai menambahkan menu baru.</p>
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>
            </main>

        </div>

    </div>

    {{-- Script SweetAlert2 untuk Konfirmasi Hapus --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-4 py-2.5'
                }
            });
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Menu Sudah Ada',
                text: @json($errors->first()),
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-4 py-2.5'
                }
            });
        });
    </script>
    @endif

    <script>
        function confirmDelete(event, buttonElement, menuName) {
            event.stopPropagation();
            
            const form = buttonElement.closest('form');

            Swal.fire({
                title: 'Hapus Menu?',
                text: `Apakah kamu yakin ingin menghapus menu "${menuName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-4 py-2.5',
                    cancelButton: 'rounded-xl text-xs font-bold px-4 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        }
    </script>

@endsection