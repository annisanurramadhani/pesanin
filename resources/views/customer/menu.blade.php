<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - {{ $merchant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-2xl relative pb-32">
        
        <form action="{{ route('customer.checkout', $qrCode->code_hash) }}" method="POST" id="orderForm">
            @csrf
            <input type="hidden" name="payment_method" value="qris">

            <!-- ================= STICKY HEADER ================= -->
            <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
                
                <div class="bg-slate-900 text-white p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center font-black text-slate-950 text-lg shadow">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>
                        <div>
                            <h1 class="font-extrabold text-base leading-tight">{{ $merchant->name }}</h1>
                            <span class="inline-block text-amber-400 text-[10px] font-bold">
                                <i class="fa-solid fa-chair mr-1"></i>{{ $qrCode->name }} • Self Order
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border-b border-slate-100 space-y-2">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-amber-500 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="customer_name" class="w-full bg-white border border-slate-200 rounded-xl pl-8 pr-3 py-2 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Masukkan nama pemesan..." required>
                    </div>

                    <div class="px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-qrcode text-amber-600 text-xs"></i>
                            <span class="text-[10px] font-extrabold text-slate-700">Pembayaran via QRIS</span>
                        </div>
                        <span class="bg-amber-500 text-slate-950 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Wajib</span>
                    </div>
                </div>

                <!-- TAB KATEGORI -->
                <div class="flex items-center gap-2 px-3 py-2 overflow-x-auto no-scrollbar bg-white">
                    <button type="button" onclick="filterCategory('all')" id="tab-all" class="category-tab px-4 py-1.5 bg-amber-500 text-slate-950 font-black rounded-full text-xs whitespace-nowrap transition shadow-sm">
                        Semua
                    </button>
                    @foreach($categories as $category)
                        <button type="button" onclick="filterCategory({{ $category->id }})" id="tab-{{ $category->id }}" class="category-tab px-4 py-1.5 bg-slate-100 text-slate-600 font-bold rounded-full text-xs whitespace-nowrap transition hover:bg-slate-200">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- ================= DAFTAR MENU ================= -->
            <div class="p-4 space-y-6">
                @if(session('error'))
                    <div class="p-3 bg-rose-100 text-rose-800 text-xs font-bold rounded-xl border border-rose-200">
                        {{ session('error') }}
                    </div>
                @endif

                @foreach($categories as $category)
                    @php
                        $categoryMenus = $menus->where('category_id', $category->id);
                    @endphp

                    @if($categoryMenus->count() > 0)
                        <div class="category-group space-y-3" id="category-group-{{ $category->id }}">
                            <h2 class="font-black text-slate-900 text-xs tracking-wider border-l-4 border-amber-500 pl-2 uppercase">
                                {{ $category->name }}
                            </h2>

                            <div class="grid grid-cols-1 gap-3">
                                @foreach($categoryMenus as $menu)
                                    @php
                                        // Pengecekan status ketersediaan & stok dari database
                                        $isHabis = !$menu->is_available || $menu->stock <= 0;
                                    @endphp

                                    <!-- Tambahkan opacity, grayscale, dan matikan klik jika habis -->
                                    <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3 transition {{ $isHabis ? 'opacity-50 grayscale select-none' : '' }}">
                                        
                                        <!-- Gambar Menu -->
                                        <div class="w-16 h-16 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0 relative border border-slate-100">
                                            @if(!empty($menu->image_url) || !empty($menu->image))
                                                <img src="{{ asset('storage/' . ($menu->image_url ?? $menu->image)) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <i class="fa-solid fa-utensils text-lg"></i>
                                                </div>
                                            @endif

                                            <!-- Overlay Badge Habis di Gambar -->
                                            @if($isHabis)
                                                <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center">
                                                    <span class="bg-rose-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-wider">Habis</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Detail Nama & Harga -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-black text-slate-900 text-xs truncate">{{ $menu->name }}</h3>
                                            <p class="text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-tight">{{ $menu->description ?? 'Sensasi nikmat siap disajikan.' }}</p>
                                            
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="font-black text-amber-600 text-xs">
                                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                                </span>
                                                <!-- Tampilkan Sisa Stok jika masih ada -->
                                                @if(!$isHabis && isset($menu->stock))
                                                    <span class="text-[9px] font-extrabold text-slate-400 bg-slate-50 border border-slate-100 px-1.5 py-0.5 rounded-md">
                                                        Sisa: {{ $menu->stock }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Pengatur Kuantitas atau Label Habis -->
                                        @if($isHabis)
                                            <div class="flex-shrink-0">
                                                <span class="text-[9px] font-black text-rose-500 bg-rose-50 px-2 py-1.5 rounded-lg border border-rose-100">KOSONG</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl flex-shrink-0">
                                                <button type="button" onclick="updateQty({{ $menu->id }}, -1, {{ $menu->price }}, {{ $menu->stock ?? 999 }})" class="w-6 h-6 bg-white text-slate-800 font-black rounded-lg shadow-sm flex items-center justify-center text-xs active:scale-95 transition">-</button>
                                                <span id="qty-{{ $menu->id }}" class="font-black text-xs w-4 text-center">0</span>
                                                <button type="button" onclick="updateQty({{ $menu->id }}, 1, {{ $menu->price }}, {{ $menu->stock ?? 999 }})" class="w-6 h-6 bg-amber-500 text-slate-950 font-black rounded-lg shadow-sm flex items-center justify-center text-xs active:scale-95 transition">+</button>
                                            </div>
                                            <div id="inputs-{{ $menu->id }}"></div>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- ================= BOTTOM NAVIGATION ================= -->
            <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-white border-t border-slate-200 p-4 shadow-2xl flex items-center justify-between z-50">
                <div>
                    <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider">Total Pesanan</span>
                    <span id="totalDisplay" class="font-black text-slate-900 text-base">Rp 0</span>
                </div>

                <button type="submit" id="submitBtn" disabled class="bg-slate-200 text-slate-400 font-black px-6 py-3 rounded-xl text-xs transition cursor-not-allowed">
                    Pesan & Bayar QRIS <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>
        </form>

    </div>

    <!-- JavaScript Logika Keranjang & Filter Tab -->
    <script>
        let cart = {};

        function filterCategory(catId) {
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.className = "category-tab px-4 py-1.5 bg-slate-100 text-slate-600 font-bold rounded-full text-xs whitespace-nowrap transition hover:bg-slate-200";
            });

            if (catId === 'all') {
                document.getElementById('tab-all').className = "category-tab px-4 py-1.5 bg-amber-500 text-slate-950 font-black rounded-full text-xs whitespace-nowrap transition shadow-sm";
                document.querySelectorAll('.category-group').forEach(group => group.style.display = 'block');
            } else {
                document.getElementById('tab-' + catId).className = "category-tab px-4 py-1.5 bg-amber-500 text-slate-950 font-black rounded-full text-xs whitespace-nowrap transition shadow-sm";
                document.querySelectorAll('.category-group').forEach(group => group.style.display = 'none');
                let targetGroup = document.getElementById('category-group-' + catId);
                if (targetGroup) targetGroup.style.display = 'block';
            }
        }

        // Tambahan param 'maxStock' agar pesanan tidak bisa melebihi sisa stok di database
        function updateQty(menuId, change, price, maxStock) {
            if (!cart[menuId]) {
                cart[menuId] = { qty: 0, price: price };
            }

            let newQty = cart[menuId].qty + change;

            // Validasi Maksimal Stok
            if (newQty > maxStock) {
                alert("Maaf, stok hanya tersisa " + maxStock + " porsi.");
                return;
            }

            cart[menuId].qty = newQty;

            if (cart[menuId].qty <= 0) {
                delete cart[menuId];
                document.getElementById('qty-' + menuId).innerText = 0;
                document.getElementById('inputs-' + menuId).innerHTML = '';
            } else {
                document.getElementById('qty-' + menuId).innerText = cart[menuId].qty;
                document.getElementById('inputs-' + menuId).innerHTML = `
                    <input type="hidden" name="items[${menuId}][menu_id]" value="${menuId}">
                    <input type="hidden" name="items[${menuId}][quantity]" value="${cart[menuId].qty}">
                `;
            }

            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            let itemCount = 0;

            for (let id in cart) {
                total += cart[id].qty * cart[id].price;
                itemCount += cart[id].qty;
            }

            document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');

            let btn = document.getElementById('submitBtn');
            if (itemCount > 0) {
                btn.disabled = false;
                btn.className = "bg-amber-500 hover:bg-amber-400 text-slate-950 font-black px-6 py-3 rounded-xl text-xs transition shadow-lg shadow-amber-500/20 active:scale-95";
            } else {
                btn.disabled = true;
                btn.className = "bg-slate-200 text-slate-400 font-black px-6 py-3 rounded-xl text-xs transition cursor-not-allowed";
            }
        }
    </script>
</body>
</html>