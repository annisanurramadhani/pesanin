<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                    Dashboard Merchant
                </h2>
                <p class="text-xs font-medium text-slate-500 mt-1">Pantau aktivitas penjualan, pesanan masuk, dan performa kafemu secara real-time.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center gap-2 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    Sistem Pesanan On
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">

        <!-- Stat Cards Grid (Lebih Besar & Berwarna Mewah) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1: Total Menu -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/10 rounded-full group-hover:scale-125 transition duration-300"></div>
                <div class="space-y-1 relative z-10">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Menu Aktif</p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tight">{{ $totalMenu ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 font-medium pt-1">Produk siap dipesan</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl shadow-lg shadow-amber-500/20 font-black relative z-10">
                    <i class="fa-solid fa-utensils"></i>
                </div>
            </div>

            <!-- Card 2: Pesanan Hari Ini -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full group-hover:scale-125 transition duration-300"></div>
                <div class="space-y-1 relative z-10">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Pesanan Hari Ini</p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tight">{{ $todayOrders ?? 0 }}</h3>
                    <p class="text-xs text-emerald-600 font-bold pt-1 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up"></i> Transaksi baru masuk
                    </p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-xl shadow-lg shadow-emerald-500/20 font-black relative z-10">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
            </div>

            <!-- Card 3: Total Transaksi -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 flex items-center justify-between relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/10 rounded-full group-hover:scale-125 transition duration-300"></div>
                <div class="space-y-1 relative z-10">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Total Keseluruhan</p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tight">{{ $totalOrders ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 font-medium pt-1">Riwayat semua order</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/20 font-black relative z-10">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

        </div>

        <!-- Section Tabel Pesanan Terbaru (Lebar & Sleek) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-lg">Pesanan Terbaru Masuk</h3>
                    <p class="text-xs text-slate-400 mt-0.5">5 transaksi paling akhir dari pelanggan meja</p>
                </div>
                <a href="{{ route('merchant.orders.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-sm hover:shadow flex items-center gap-2">
                    <span>Lihat Semua Orders</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="p-4 pl-6">No. Order & Meja</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Pesanan</th>
                            <th class="p-4">Total Harga</th>
                            <th class="p-4 pr-6">Status Pesanan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders ?? [] as $order)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4 pl-6">
                                    <span class="font-black text-slate-900 block text-base">#{{ $order->order_number }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-md mt-1">
                                        <i class="fa-solid fa-location-dot"></i> {{ $order->qrCode->name ?? 'Meja General' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-800">{{ $order->customer_name }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ $order->created_at->format('H:i Waktu') }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($order->items as $item)
                                            <span class="bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-lg text-xs">
                                                {{ $item->menu->name ?? 'Menu' }} <b class="text-slate-900 font-black">x{{ $item->quantity }}</b>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="font-black text-slate-900 text-base">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="p-4 pr-6">
                                    @if($order->status == 'menunggu')
                                        <span class="px-3 py-1.5 text-xs bg-amber-50 text-amber-700 font-extrabold rounded-xl border border-amber-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-clock"></i> Menunggu
                                        </span>
                                    @elseif($order->status == 'diproses')
                                        <span class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 font-extrabold rounded-xl border border-blue-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-fire"></i> Diproses
                                        </span>
                                    @elseif($order->status == 'selesai')
                                        <span class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 font-extrabold rounded-xl border border-emerald-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-check"></i> Selesai
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 text-xs bg-rose-50 text-rose-700 font-extrabold rounded-xl border border-rose-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-xmark"></i> Batal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum ada pesanan masuk</p>
                                    <p class="text-xs text-slate-400 mt-1">Setiap pesanan baru dari scan QR pelanggan akan muncul di sini secara langsung.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>