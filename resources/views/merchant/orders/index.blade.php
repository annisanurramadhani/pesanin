<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                    Kelola Pesanan Masuk
                </h2>
                <p class="text-xs font-medium text-slate-500 mt-1">Pantau pesanan dari meja pelanggan secara real-time dan perbarui status pesanan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-slate-900 text-amber-400 border border-slate-800 shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-receipt"></i>
                    Total: {{ count($orders) }} Order
                </span>
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
                    <thead class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">
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
                                    <span class="font-black text-slate-900 block text-base">#{{ $order->order_number }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-md mt-1 border border-amber-200/60">
                                        <i class="fa-solid fa-location-dot"></i> {{ $order->qrCode->name ?? 'Meja General' }}
                                    </span>
                                </td>

                                <!-- Pelanggan & Jam -->
                                <td class="p-4">
                                    <p class="font-extrabold text-slate-800">{{ $order->customer_name }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                                        <i class="fa-regular fa-clock"></i> {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </td>

                                <!-- Item Pesanan -->
                                <td class="p-4">
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span class="font-semibold text-slate-700">{{ $item->menu->name ?? 'Menu' }}</span>
                                                <span class="font-black text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-[10px]">(x{{ $item->quantity }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Total Harga -->
                                <td class="p-4">
                                    <span class="font-black text-slate-900 text-base">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </td>

                                <!-- Status Badge -->
                                <td class="p-4">
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

                                <!-- Form Aksi Update Status -->
                                <td class="p-4 pr-6">
                                    <form action="{{ route('merchant.orders.status', $order->id) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="bg-slate-50 border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 rounded-xl text-xs font-bold py-2 px-3 text-slate-800 transition cursor-pointer">
                                            <option value="menunggu" {{ $order->status == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                                            <option value="diproses" {{ $order->status == 'diproses' ? 'selected' : '' }}>🔥 Diproses</option>
                                            <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                            <option value="dibatalkan" {{ $order->status == 'dibatalkan' ? 'selected' : '' }}>❌ Batalkan</option>
                                        </select>
                                        <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-amber-400 p-2 rounded-xl text-xs font-extrabold transition shadow-sm active:scale-[0.95]" title="Simpan Perubahan">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Belum ada pesanan masuk dari pelanggan</p>
                                    <p class="text-xs text-slate-400 mt-1">Saat pelanggan melakukan checkout pesanan, data transaksi akan tampil otomatis di tabel ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($orders, 'links'))
                <div class="p-4 border-t border-slate-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Elemen Audio Tersembunyi (Memakai sound bell gratis dari mixkit) -->
<audio id="notificationSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Simpan jumlah pesanan awal saat halaman pertama dimuat
        // (Pastikan variabel $activeOrdersCount dikirim dari OrderController::index, 
        // atau kita pakai angka sementara 0 yang akan ter-update otomatis dalam 10 detik)
        let currentOrderCount = -1; 

        setInterval(() => {
            fetch("{{ route('merchant.orders.check') }}")
                .then(response => response.json())
                .then(data => {
                    // Inisialisasi awal
                    if (currentOrderCount === -1) {
                        currentOrderCount = data.count;
                        return;
                    }

                    // Jika jumlah pesanan aktif bertambah (Ada pesanan baru)
                    if (data.count > currentOrderCount) {
                        // 1. Bunyikan Suara
                        document.getElementById('notificationSound').play().catch(error => {
                            console.log("Browser memblokir autoplay suara sampai user berinteraksi dengan halaman.");
                        });
                        
                        // 2. Update jumlah
                        currentOrderCount = data.count;

                        // 3. Refresh halaman setelah 1.5 detik agar suara sempat berbunyi
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } 
                    // Jika pesanan selesai (berkurang), cukup update variabel tanpa refresh paksa
                    else if (data.count < currentOrderCount) {
                        currentOrderCount = data.count;
                    }
                })
                .catch(error => console.error('Error checking new orders:', error));
        }, 10000); // Mengecek database setiap 10 detik (10.000 ms)
    });
</script>
</x-app-layout>