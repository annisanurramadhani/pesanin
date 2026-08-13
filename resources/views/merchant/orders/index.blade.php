@extends('layouts.merchant')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                Kelola Pesanan Masuk
            </h2>
            <p class="text-xs font-medium text-slate-500 mt-1">Pantau pesanan dari meja pelanggan secara real-time dan
                perbarui status pesanan.</p>
        </div>
        <div class="flex items-center gap-2">
            <span
                class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-slate-900 text-amber-400 border border-slate-800 shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i>
                Total: {{ method_exists($orders, 'total') ? $orders->total() : count($orders) }} Order
            </span>
        </div>
    </div>
@endsection

@section('content')

    <div class="space-y-8">

        <!-- Notifikasi Sukses -->
        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-emerald-50 border border-emerald-200/80 text-emerald-800 px-5 py-4 rounded-2xl text-sm font-bold shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Card Container Utama Tabel Pesanan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-lg">Daftar Transaksi Pelanggan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Semua riwayat pesanan yang dipesan melalui scan QR meja</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead
                        class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="p-4 pl-6">No. Order & Meja</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Detail Item Menu</th>
                            <th class="p-4">Total Harga</th>
                            <th class="p-4">Status Saat Ini</th>
                            <th class="p-4 pr-6">Aksi Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50/60 transition">
                                <!-- No Order & Meja -->
                                <td class="p-4 pl-6">
                                    <span
                                        class="font-black text-slate-900 block text-base">#{{ $order->order_number }}</span>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-md mt-1 border border-amber-200/60">
                                        <i class="fa-solid fa-location-dot"></i>
                                        {{ $order->qrCode->name ?? 'Meja General' }}
                                    </span>
                                </td>

                                <!-- Pelanggan & Jam -->
                                <td class="p-4">
                                    <p class="font-extrabold text-slate-800">{{ $order->customer_name }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $order->created_at->addHours(7)->format('d M Y, H:i') }} WIB
                                    </p>
                                </td>

                                <!-- Item Pesanan -->
                                <td class="p-4">
                                    <div class="space-y-1">
                                        @foreach ($order->items as $item)
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span
                                                    class="font-semibold text-slate-700">{{ $item->menu->name ?? 'Menu' }}</span>
                                                <span
                                                    class="font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[10px]">(x{{ $item->quantity }})</span>
                                            </div>
                                            @if (!empty($item->notes))
                                                <p class="text-[10px] text-amber-600 italic pl-3.5">- Note:
                                                    {{ $item->notes }}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Total Harga (Perbaikan dari total_amount -> total_price) -->
                                <td class="p-4">
                                    <span class="font-black text-slate-900 text-base">Rp
                                        {{ number_format($order->total_amount ?? ($order->total_price ?? 0), 0, ',', '.') }}</span>
                                </td>
                                <!-- Status Badge (Mendukung bahasa Inggris & Indonesia) -->
                                <td class="p-4">
                                    @if (in_array($order->status, ['pending', 'menunggu']))
                                        <span
                                            class="px-3 py-1.5 text-xs bg-amber-50 text-amber-700 font-extrabold rounded-xl border border-amber-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-clock"></i> Menunggu
                                        </span>
                                    @elseif(in_array($order->status, ['processing', 'diproses']))
                                        <span
                                            class="px-3 py-1.5 text-xs bg-blue-50 text-blue-700 font-extrabold rounded-xl border border-blue-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-fire"></i> Diproses
                                        </span>
                                    @elseif(in_array($order->status, ['completed', 'selesai']))
                                        <span
                                            class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 font-extrabold rounded-xl border border-emerald-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-check"></i> Selesai
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1.5 text-xs bg-rose-50 text-rose-700 font-extrabold rounded-xl border border-rose-200/80 inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-xmark"></i> Batal
                                        </span>
                                    @endif
                                </td>

                                <!-- Form Aksi Update Status -->
                                <td class="p-4 pr-6">
                                    <div class="flex items-center gap-2">
                                        <!-- Tombol Cetak Struk -->
                                        <a href="{{ route('merchant.orders.receipt', $order->id) }}" target="_blank"
                                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 p-2 rounded-xl text-xs font-extrabold transition"
                                            title="Cetak Struk">
                                            <i class="fa-solid fa-print"></i>
                                        </a>

                                        <form action="{{ route('merchant.orders.status', $order->id) }}" method="POST"
                                            class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status"
                                                class="bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 rounded-xl text-xs font-bold py-2 px-3 text-slate-800 transition cursor-pointer">
                                                <option value="menunggu"
                                                    {{ $order->status == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu
                                                </option>
                                                <option value="diproses"
                                                    {{ $order->status == 'diproses' ? 'selected' : '' }}>🔥 Diproses
                                                </option>
                                                <option value="selesai"
                                                    {{ $order->status == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                                <option value="dibatalkan"
                                                    {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>❌ Batalkan
                                                </option>
                                            </select>
                                            <button type="submit"
                                                class="bg-slate-900 hover:bg-slate-800 text-amber-400 p-2 rounded-xl text-xs font-extrabold transition shadow-sm active:scale-[0.95]"
                                                title="Simpan Perubahan">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div
                                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum ada pesanan masuk dari pelanggan</p>
                                    <p class="text-xs text-slate-400 mt-1">Saat pelanggan melakukan checkout pesanan, data
                                        transaksi akan tampil otomatis di tabel ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($orders, 'links'))
                <div class="p-4 border-t border-slate-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Elemen Audio Notifikasi (Mixkit Bell) -->
    <audio id="notifSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Script Polling Real-time Pesanan Baru -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let lastOrderCount = null;

            function checkNewOrders() {
                fetch("{{ route('merchant.orders.check') }}")
                    .then(response => response.json())
                    .then(data => {
                        if (lastOrderCount !== null && data.count > lastOrderCount) {
                            // 1. Putar Suara Notifikasi
                            const audio = document.getElementById('notifSound');
                            if (audio) {
                                audio.play().catch(e => console.log(
                                    'Audio autoplay diblokir browser sampai ada interaksi user.'));
                            }

                            // 2. Auto Reload Halaman setelah 1 detik
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                        lastOrderCount = data.count;
                    })
                    .catch(err => console.error('Error checking new orders:', err));
            }

            // Cek pesanan baru setiap 5 detik
            setInterval(checkNewOrders, 5000);
            checkNewOrders();
        });
    </script>
@endsection
