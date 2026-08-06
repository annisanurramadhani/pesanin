<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                    Kelola Menu & Kategori
                </h2>
                <p class="text-xs font-medium text-slate-500 mt-1">Atur katalog produk, kategori makanan/minuman, dan daftar harga tokomu.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">

        <!-- Notifikasi Sukses -->
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200/80 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Sidebar Forms (4 Columns) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Form Tambah Kategori -->
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
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama Kategori *</label>
                            <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400" placeholder="Contoh: Coffee & Espresso" required>
                        </div>
                        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-200 shadow-md active:scale-[0.98]">
                            + Simpan Kategori
                        </button>
                    </form>
                </div>

                <!-- Form Tambah Menu -->
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

                    <!-- PENTING: enctype="multipart/form-data" ditambahkan agar file gambar bisa ter-upload -->
                    <form action="{{ route('merchant.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pilih Kategori *</label>
                            <select name="category_id" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama Menu *</label>
                            <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400" placeholder="Contoh: Ice Americano PST" required>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Harga (Rp) *</label>
                            <input type="number" name="price" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400" placeholder="22000" required>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Foto Produk (Opsional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 rounded-xl text-xs p-2.5 text-slate-700 transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-slate-950 hover:file:bg-amber-400 cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Deskripsi (Opsional)</label>
                            <textarea name="description" rows="2" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400" placeholder="Keterangan singkat produk..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3.5 px-4 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-amber-500/25 active:scale-[0.98]">
                            + Tambahkan Ke Katalok
                        </button>
                    </form>
                </div>

            </div>

            <!-- Right Main Catalogue Grid (8 Columns) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-lg">Katalog Menu Aktif</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar semua makanan dan minuman yang siap dipesan</p>
                    </div>
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-extrabold border border-amber-200/60">
                        Total {{ count($menus) }} Produk
                    </span>
                </div>

                <!-- Card Grid View Modern -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($menus as $menu)
                        <div class="p-5 rounded-2xl border border-slate-200/70 bg-slate-50/50 hover:bg-white hover:border-amber-500/50 hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <span class="px-3 py-1 bg-slate-900 text-amber-400 font-extrabold rounded-lg text-[10px] uppercase tracking-wider">
                                        {{ $menu->category->name ?? 'Uncategorized' }}
                                    </span>
                                    <form action="{{ route('merchant.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition" title="Hapus Menu">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Thumbnail Foto Menu & Info Produk -->
                                <div class="flex gap-3 mb-3">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-xl bg-slate-200/60 text-slate-400 flex items-center justify-center shrink-0 font-bold text-lg">
                                            <i class="fa-solid fa-mug-hot"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <h4 class="font-extrabold text-slate-900 text-base group-hover:text-amber-600 transition leading-tight">{{ $menu->name }}</h4>
                                        <p class="text-xs text-slate-500 font-medium line-clamp-2 mt-1">{{ $menu->description ?? 'Tidak ada deskripsi.' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-400">Harga Jual:</span>
                                <span class="font-black text-slate-900 text-base">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 p-12 text-center">
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

    </div>
</x-app-layout>