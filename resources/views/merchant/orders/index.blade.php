@extends('layouts.merchant')

@section('header')
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                {{ Auth::user()->role === 'kasir' ? 'Kelola Pesanan Masuk' : 'Riwayat Pesanan' }}
            </h2>
            <p class="text-xs font-medium text-slate-500 mt-1">
                {{ Auth::user()->role === 'kasir' ? 'Validasi dan perbarui status pesanan pelanggan hari ini secara real-time.' : 'Pantau seluruh riwayat transaksi pelanggan dan rekap pendapatan.' }}
            </p>
        </div>

        <!-- FORM FILTER (Dapat diakses Owner & Kasir) -->
        <form method="GET" action="{{ route('merchant.orders.index') }}" class="flex items-center gap-2 bg-slate-900 p-2 rounded-2xl shadow-lg border border-slate-800">
            <div class="pl-2 pr-1 text-amber-400 text-xs font-extrabold flex items-center gap-1.5">
                <i class="fa-solid fa-sliders"></i>
                <span class="hidden sm:inline text-slate-300">Filter:</span>
            </div>

            <select name="filter_type" id="filterTypeSelect" onchange="switchFilterMode(this.value)"
                class="bg-slate-800 text-amber-400 text-xs font-bold rounded-xl px-3 py-2 border border-slate-700/80 focus:outline-none focus:ring-2 focus:ring-amber-500 cursor-pointer">
                <option value="day" {{ ($filterType ?? 'day') === 'day' ? 'selected' : '' }}>📅 Per Hari</option>
                <option value="month" {{ ($filterType ?? '') === 'month' ? 'selected' : '' }}>🗓️ Per Bulan</option>
                <option value="year" {{ ($filterType ?? '') === 'year' ? 'selected' : '' }}>📊 Per Tahun</option>
            </select>

            <div id="inputDayWrapper" class="{{ ($filterType ?? 'day') === 'day' ? 'block' : 'hidden' }}">
                <input type="date" name="date" value="{{ $selectedDate ?? date('Y-m-d') }}" 
                    class="bg-slate-800 text-white text-xs font-bold rounded-xl px-3 py-2 border border-slate-700/80 focus:outline-none">
            </div>

            <div id="inputMonthWrapper" class="{{ ($filterType ?? '') === 'month' ? 'block' : 'hidden' }}">
                <input type="month" name="month" value="{{ $selectedMonth ?? date('Y-m-m') }}" 
                    class="bg-slate-800 text-white text-xs font-bold rounded-xl px-3 py-2 border border-slate-700/80 focus:outline-none">
            </div>

            <div id="inputYearWrapper" class="{{ ($filterType ?? '') === 'year' ? 'block' : 'hidden' }}">
                <select name="year" class="bg-slate-800 text-white text-xs font-bold rounded-xl px-3 py-2 border border-slate-700/80 focus:outline-none">
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ ($selectedYear ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 px-4 py-2 rounded-xl text-xs font-black transition cursor-pointer">
                Terapkan
            </button>
        </form>
    </div>
@endsection

@section('content')

    <div class="space-y-6">

        @if(Auth::user()->role !== 'kasir')
            <!-- REKAP PENDAPATAN (KHUSUS OWNER) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500/10 text-amber-600 rounded-2xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Pesanan</span>
                        <span class="text-2xl font-black text-slate-900">{{ $totalOrders }} Order</span>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center text-xl font-bold">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Pendapatan ({{ $labelPeriode ?? 'Hari Ini' }})</span>
                        <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- TABEL PESANAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-lg">
                        {{ Auth::user()->role === 'kasir' ? 'Antrean Pesanan Masuk' : 'Daftar Transaksi Pelanggan' }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ Auth::user()->role === 'kasir' ? 'Daftar pesanan hari ini dari meja pelanggan' : 'Semua riwayat pesanan periode ' . ($labelPeriode ?? 'Hari Ini') }}
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="p-4 pl-6">No. Order & Meja</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Detail Item Menu</th>
                            <th class="p-4">Metode Bayar</th>
                            <th class="p-4">Total Harga</th>
                            @if(Auth::user()->role === 'kasir')
                                <th class="p-4 pr-6 text-center">Aksi Cepat Kasir</th>
                            @else
                                <th class="p-4 pr-6 text-center">Cetak Struk</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                            @php
                                $itemTotal = $order->items->sum(function($item) {
                                    return $item->subtotal ?? ($item->price * $item->quantity);
                                });
                                $grandTotal = ($order->total_amount > 0) ? $order->total_amount : (($order->total_price > 0) ? $order->total_price : $itemTotal);
                                $isCash = in_array(strtolower($order->payment_method), ['cash', 'kasir', 'tunai']);
                                $statusStr = strtolower($order->status ?? '');
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="p-4 pl-6">
                                    <span class="font-black text-slate-900 block text-base">#{{ $order->order_number }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-md mt-1 border border-amber-200/60">
                                        <i class="fa-solid fa-location-dot"></i> {{ $order->qrCode->name ?? 'Meja General' }}
                                    </span>
                                </td>

                                <td class="p-4">
                                    <p class="font-extrabold text-slate-800">{{ $order->customer_name }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        <i class="fa-regular fa-clock"></i> {{ $order->created_at->format('H:i') }} WIB
                                    </p>
                                </td>

                                <td class="p-4">
                                    <div class="space-y-1">
                                        @foreach ($order->items as $item)
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span class="font-semibold text-slate-700">{{ $item->menu_name ?? $item->menu->name ?? 'Menu' }}</span>
                                                <span class="font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[10px]">(x{{ $item->quantity }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="p-4">
                                    @if($isCash)
                                        <span class="px-3 py-1 text-xs bg-slate-100 text-slate-800 font-extrabold rounded-xl border border-slate-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-cash-register text-slate-500"></i> Bayar Kasir
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs bg-amber-50 text-amber-700 font-extrabold rounded-xl border border-amber-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-qrcode text-amber-600"></i> QRIS
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4">
                                    <span class="font-black text-slate-900 text-base">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- KOLOM AKSI BERDASARKAN ROLE -->
                                <td class="p-4 pr-6 text-center">
                                    @if(Auth::user()->role === 'kasir')
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Print Struk -->
                                            <a href="{{ route('merchant.orders.receipt', encryptId($order->id)) }}" target="_blank"
                                                class="w-8 h-8 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl flex items-center justify-center text-xs transition" title="Cetak Struk">
                                                <i class="fa-solid fa-print"></i>
                                            </a>

                                            @if(in_array($statusStr, ['selesai', 'completed']))
                                                <!-- Status Selesai -->
                                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-black rounded-xl border border-emerald-200 inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-check"></i> Lunas & Selesai
                                                </span>
                                            @elseif(in_array($statusStr, ['batal', 'cancelled']))
                                                <!-- Status Batal -->
                                                <span class="px-3 py-1 bg-rose-100 text-rose-800 text-xs font-black rounded-xl border border-rose-200 inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-xmark"></i> Dibatalkan
                                                </span>
                                            @else
                                                <!-- Tombol Validasi Cepat Kasir -->
                                                <form action="{{ route('merchant.orders.status', encryptId($order->id)) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-black transition shadow-sm active:scale-95 flex items-center gap-1 cursor-pointer">
                                                        <i class="fa-solid fa-circle-check"></i> Selesaikan
                                                    </button>
                                                </form>

                                                <form action="{{ route('merchant.orders.status', encryptId($order->id)) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="batal">
                                                    <button type="submit" onclick="return confirm('Batalkan pesanan ini?')" class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl text-xs font-extrabold transition cursor-pointer" title="Batalkan">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <!-- CETAK STRUK UNTUK OWNER -->
                                        <a href="{{ route('merchant.orders.receipt', encryptId($order->id)) }}" target="_blank"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition shadow-sm" title="Cetak Struk">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum ada pesanan masuk</p>
                                    <p class="text-xs text-slate-400 mt-1">Pesanan pelanggan akan otomatis muncul di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function switchFilterMode(mode) {
            document.getElementById('inputDayWrapper')?.classList.add('hidden');
            document.getElementById('inputMonthWrapper')?.classList.add('hidden');
            document.getElementById('inputYearWrapper')?.classList.add('hidden');

            if (mode === 'day') document.getElementById('inputDayWrapper')?.classList.remove('hidden');
            else if (mode === 'month') document.getElementById('inputMonthWrapper')?.classList.remove('hidden');
            else if (mode === 'year') document.getElementById('inputYearWrapper')?.classList.remove('hidden');
        }
    </script>
@endsection