<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - {{ $qrCode->merchant->name ?? 'PesanIn' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased pb-32">

    <!-- Container Utama -->
    <div class="max-w-md md:max-w-2xl mx-auto bg-white min-h-screen shadow-xl relative border-x border-slate-200/60">

        <!-- HEADER KAFE PREMIUM -->
        <div class="bg-slate-900 text-white p-6 rounded-b-3xl shadow-lg relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl"></div>
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-2xl shadow-lg shadow-amber-500/30 shrink-0">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-white">{{ $qrCode->merchant->name ?? 'Kafe/Resto' }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            <i class="fa-solid fa-location-dot text-[10px]"></i>
                            {{ $qrCode->name }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">• Self Order</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM UTAMA PEMESANAN -->
        <form action="{{ route('customer.checkout', $qrCode->code_hash) }}" method="POST" id="orderForm" class="p-6 space-y-6">
            @csrf

            <!-- INPUT NAMA PELANGGAN -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-user text-amber-500 mr-1"></i> Nama Pemesan *
                </label>
                <input type="text" name="customer_name" class="w-full bg-white border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 rounded-xl text-sm p-3 text-slate-800 font-bold transition placeholder:font-normal placeholder:text-slate-400" placeholder="Masukkan nama kamu di sini..." required>
            </div>

            <!-- KATALOG MENU PER KATEGORI -->
            <div class="space-y-6">
                @forelse($categories as $category)
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="w-2 h-4 bg-amber-500 rounded-full"></span>
                            <h2 class="font-extrabold text-slate-900 text-base">{{ $category->name }}</h2>
                        </div>

                        <div class="space-y-3">
                            @foreach($category->menus as $menu)
                                <div class="p-4 rounded-2xl border border-slate-200/80 bg-white hover:border-amber-500/50 transition-all duration-200 flex items-center justify-between gap-4 shadow-sm">
                                    <div class="space-y-1 flex-1">
                                        <h3 class="font-extrabold text-slate-900 text-sm">{{ $menu->name }}</h3>
                                        @if($menu->description)
                                            <p class="text-xs text-slate-400 font-medium line-clamp-2">{{ $menu->description }}</p>
                                        @endif
                                        <p class="font-black text-amber-600 text-sm pt-1">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <!-- COUNTER PESANAN (Kuantitas) -->
                                    <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-xl border border-slate-200/60 shrink-0">
                                        <button type="button" onclick="updateQty('qty_{{ $menu->id }}', -1, {{ $menu->price }})" class="w-7 h-7 rounded-lg bg-white text-slate-700 font-black flex items-center justify-center hover:bg-rose-500 hover:text-white transition shadow-sm active:scale-90">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>

                                        <input type="number" name="items[{{ $menu->id }}]" id="qty_{{ $menu->id }}" value="0" min="0" class="w-8 text-center bg-transparent font-black text-slate-900 text-sm focus:outline-none" readonly>

                                        <button type="button" onclick="updateQty('qty_{{ $menu->id }}', 1, {{ $menu->price }})" class="w-7 h-7 rounded-lg bg-amber-500 text-slate-950 font-black flex items-center justify-center hover:bg-amber-400 transition shadow-sm active:scale-90">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <i class="fa-solid fa-utensils text-4xl text-slate-300 mb-2"></i>
                        <p class="font-bold text-slate-600 text-sm">Belum ada menu tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- FLOATING BAR KERANJANG MELAYANG -->
            <div class="fixed bottom-0 left-0 right-0 p-4 bg-white/90 backdrop-blur-md border-t border-slate-200/80 shadow-2xl z-50 max-w-md md:max-w-2xl mx-auto">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Pesanan</p>
                        <p class="text-lg font-black text-slate-900" id="totalPriceDisplay">Rp 0</p>
                    </div>

                    <button type="submit" id="btnCheckout" class="flex-1 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3.5 px-6 rounded-2xl text-sm transition-all duration-200 shadow-lg shadow-amber-500/25 active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>Pesan Sekarang</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <!-- JAVASCRIPT KALKULASI -->
    <script>
        let totalPrice = 0;

        function updateQty(inputId, change, price) {
            const input = document.getElementById(inputId);
            let currentVal = parseInt(input.value) || 0;
            
            if (currentVal + change >= 0) {
                currentVal += change;
                input.value = currentVal;
                
                totalPrice += (change * price);
                if(totalPrice < 0) totalPrice = 0;

                document.getElementById('totalPriceDisplay').innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');
            }
        }
    </script>
</body>
</html>