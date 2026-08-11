<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">Edit Detail Menu</h2>
                <p class="text-xs font-medium text-slate-500 mt-1">Ubah informasi produk, harga, stok, kategori, atau foto menu.</p>
            </div>
            <a href="{{ route('merchant.menu.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-xl text-xs transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-5">
            
            <form action="{{ route('merchant.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pilih Kategori *</label>
                    <select name="category_id" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white rounded-xl text-sm p-3.5 text-slate-800 font-semibold" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $menu->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white rounded-xl text-sm p-3.5 text-slate-800 font-semibold" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Harga (Rp) *</label>
                        <input type="number" name="price" value="{{ old('price', $menu->price) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white rounded-xl text-sm p-3.5 text-slate-800 font-semibold" required>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Stok (Porsi) *</label>
                        <input type="number" name="stock" value="{{ old('stock', $menu->stock ?? 0) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white rounded-xl text-sm p-3.5 text-slate-800 font-semibold" min="0" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white rounded-xl text-sm p-3.5 text-slate-800 font-semibold">{{ old('description', $menu->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Foto Saat Ini</label>
                    @if($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" class="w-20 h-20 rounded-xl object-cover mb-2 border border-slate-200">
                    @else
                        <p class="text-xs text-slate-400 mb-2">Belum ada foto.</p>
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-xs p-2.5 text-slate-700">
                </div>

                <div class="pt-3 flex gap-3">
                    <a href="{{ route('merchant.menu.index') }}" class="w-1/2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold py-3.5 rounded-xl text-sm transition">
                        Batal
                    </a>
                    <button type="submit" class="w-1/2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black py-3.5 rounded-xl text-sm shadow-lg shadow-amber-500/25 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>