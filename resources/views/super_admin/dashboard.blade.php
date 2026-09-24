@extends('layouts.admin')

@section('header')
    <div>
        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
            Super Admin Platform
        </h2>
        <p class="text-xs font-medium text-slate-500 mt-1">
            Kelola seluruh mitra kafe & merchant terdaftar di sistem PesanIn.
        </p>
    </div>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- ================= 1. KARTU STATISTIK ATAS (4 GRID) ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <!-- Card 1 -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Merchant</span>
                <div class="flex items-baseline justify-between mt-2">
                    <h3 class="text-2xl font-black text-slate-900">{{ \App\Models\Merchant::count() }}</h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-store"></i>
                    </div>
                </div>
                <p class="text-[10px] text-emerald-600 font-semibold mt-2">Aktif di sistem</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pengguna</span>
                <div class="flex items-baseline justify-between mt-2">
                    <h3 class="text-2xl font-black text-slate-900">{{ \App\Models\User::count() }}</h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
                <p class="text-[10px] text-emerald-600 font-semibold mt-2">Owner / Kasir / Staff</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Transaksi</span>
                <div class="flex items-baseline justify-between mt-2">
                    <h3 class="text-2xl font-black text-slate-900">{{ \App\Models\Order::count() }}</h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                </div>
                <p class="text-[10px] text-emerald-600 font-semibold mt-2">Keseluruhan pesanan</p>
            </div>

            <!-- Card 4 (Penarikan Pending - Realtime ID Ditambahkan) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penarikan Pending</span>
                <div class="flex items-baseline justify-between mt-2">
                    <h3 id="stat-penarikan-pending" class="text-2xl font-black text-orange-500">
                        {{ \App\Models\Withdrawal::where('status', 'pending')->count() }}
                    </h3>
                    <div
                        class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 font-semibold mt-2">menunggu persetujuan</p>
            </div>

        </div>


        <!-- ================= 2. BAGIAN GRAFIK (2 KOLOM) ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Grafik Statistik Transaksi -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold text-slate-800 text-sm">Statistik Transaksi</h4>
                    <div class="flex bg-slate-100 p-1 rounded-xl text-[11px] font-semibold text-slate-500">
                        <button onclick="changeFilter('tx', '7days', this)"
                            class="filter-btn-tx px-3 py-1 rounded-lg transition-all">7 Hari</button>
                        <button onclick="changeFilter('tx', '30days', this)"
                            class="filter-btn-tx px-3 py-1 rounded-lg bg-white shadow-sm text-blue-600 font-bold transition-all">30
                            Hari</button>
                        <button onclick="changeFilter('tx', '3months', this)"
                            class="filter-btn-tx px-3 py-1 rounded-lg transition-all">3 Bulan</button>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mb-6">Jumlah transaksi platform dalam periode terakhir</p>
                <div
                    class="h-64 flex items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>

            <!-- Grafik Pendapatan Platform -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold text-slate-800 text-sm">Pendapatan Platform</h4>
                    <div class="flex bg-slate-100 p-1 rounded-xl text-[11px] font-semibold text-slate-500">
                        <button onclick="changeFilter('rev', '7days', this)"
                            class="filter-btn-rev px-3 py-1 rounded-lg transition-all">7 Hari</button>
                        <button onclick="changeFilter('rev', '30days', this)"
                            class="filter-btn-rev px-3 py-1 rounded-lg bg-white shadow-sm text-blue-600 font-bold transition-all">30
                            Hari</button>
                        <button onclick="changeFilter('rev', '3months', this)"
                            class="filter-btn-rev px-3 py-1 rounded-lg transition-all">3 Bulan</button>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mb-6">Total pendapatan/komisi platform</p>
                <div
                    class="h-64 flex items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

        </div>


        <!-- ================= 3. BAGIAN BAWAH (STATUS, PENARIKAN, MERCHANT TERBARU) ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- A. Status Merchant -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Status Merchant</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Ringkasan status merchant terdaftar</p>
                    <div class="my-6 flex items-center justify-center">
                        <div
                            class="relative w-36 h-36 flex items-center justify-center rounded-full border-8 border-emerald-500 flex-col">
                            <span class="text-2xl font-black text-slate-900">{{ \App\Models\Merchant::count() }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Merchant</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-2.5 pt-4 border-t border-slate-100 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2 font-medium text-slate-600"><span
                                class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Aktif</span>
                        <span
                            class="font-bold text-slate-800">{{ \App\Models\Merchant::where('status', 'active')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="flex items-center gap-2 font-medium text-slate-600"><span
                                class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Nonaktif</span>
                        <span
                            class="font-bold text-slate-800">{{ \App\Models\Merchant::where('status', 'inactive')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- B. Penarikan Saldo Terbaru -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <h4 class="font-bold text-slate-800 text-sm">Penarikan Saldo Terbaru</h4>
                        <a href="{{ route('super_admin.withdrawals.index') }}"
                            class="text-xs font-bold text-amber-600 hover:underline">Lihat Semua →</a>
                    </div>
                    <p class="text-xs text-slate-400 mb-4">Pengajuan penarikan saldo terbaru</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100">
                                    <th class="pb-2.5 font-semibold">Merchant</th>
                                    <th class="pb-2.5 font-semibold">Nominal</th>
                                    <th class="pb-2.5 font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse(\App\Models\Withdrawal::with('merchant')->latest()->take(5)->get() as $w)
                                    <tr class="border-b border-slate-50">
                                        <td class="py-2.5 font-medium text-slate-700">
                                            {{ optional($w->merchant)->name ?? 'Unknown' }}</td>
                                        <td class="py-2.5 text-slate-600">Rp {{ number_format($w->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5">
                                            @if ($w->status == 'pending')
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] bg-amber-50 text-amber-600 font-bold">Pending</span>
                                            @else
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] bg-emerald-50 text-emerald-600 font-bold">Berhasil</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-slate-400">Belum ada pengajuan
                                            penarikan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- C. Merchant Terbaru -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <h4 class="font-bold text-slate-800 text-sm">Merchant Terbaru</h4>
                        <a href="{{ route('super_admin.merchants.index') }}"
                            class="text-xs font-bold text-amber-600 hover:underline">Lihat Semua →</a>
                    </div>
                    <p class="text-xs text-slate-400 mb-4">Merchant terbaru yang bergabung</p>
                    <div class="space-y-3.5">
                        @forelse(\App\Models\Merchant::latest()->take(5)->get() as $m)
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm shrink-0">
                                    <i class="fa-solid fa-store"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h5 class="text-xs font-bold text-slate-800 truncate">{{ $m->name }}</h5>
                                    <p class="text-[10px] text-slate-400">Bergabung {{ $m->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs">Belum ada merchant</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fungsi helper untuk mengambil nama 3 bulan terakhir secara dinamis
        function getLast3Months() {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const date = new Date();
            let result = [];
            for (let i = 2; i >= 0; i--) {
                let d = new Date(date.getFullYear(), date.getMonth() - i, 1);
                result.push(months[d.getMonth()]);
            }
            return result;
        }

        const dynamicMonths = getLast3Months();

        // Data Grafik Statistik Transaksi
        const transactionDataSets = {
            '7days': {
                labels: ['18 Sep', '19 Sep', '20 Sep', '21 Sep', '22 Sep', '23 Sep', 'Hari Ini'],
                data: [10, 15, 8, 20, 12, 25, {{ \App\Models\Order::count() }}]
            },
            '30days': {
                labels: ['1 Sep', '5 Sep', '10 Sep', '15 Sep', '20 Sep', '25 Sep', '30 Sep'],
                data: [50, 40, 75, 60, 90, 70, {{ \App\Models\Order::count() }}]
            },
            '3months': {
                labels: dynamicMonths,
                data: [300, 450, {{ \App\Models\Order::count() * 3 }}]
            }
        };

        // Data Grafik Pendapatan Platform
        const revenueDataSets = {
            '7days': {
                labels: ['18 Sep', '19 Sep', '20 Sep', '21 Sep', '22 Sep', '23 Sep', 'Hari Ini'],
                data: [0.1, 0.2, 0.1, 0.3, 0.2, 0.4, 0.5]
            },
            '30days': {
                labels: ['1 Sep', '5 Sep', '10 Sep', '15 Sep', '20 Sep', '25 Sep', '30 Sep'],
                data: [0.5, 0.8, 1.2, 1.0, 1.5, 1.8, 2.2]
            },
            '3months': {
                labels: dynamicMonths,
                data: [1.8, 2.2, 2.9]
            }
        };

        // Inisialisasi Grafik Transaksi
        const ctxTx = document.getElementById('transactionChart').getContext('2d');
        const transactionChart = new Chart(ctxTx, {
            type: 'line',
            data: {
                labels: transactionDataSets['30days'].labels,
                datasets: [{
                    data: transactionDataSets['30days'].data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Inisialisasi Grafik Pendapatan
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctxRev, {
            type: 'bar',
            data: {
                labels: revenueDataSets['30days'].labels,
                datasets: [{
                    data: revenueDataSets['30days'].data,
                    backgroundColor: '#f59e0b',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Fungsi Filter Interaktif
        function changeFilter(type, period, element) {
            const buttons = document.querySelectorAll(`.filter-btn-${type}`);
            buttons.forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600', 'font-bold');
            });
            element.classList.add('bg-white', 'shadow-sm', 'text-blue-600', 'font-bold');

            if (type === 'tx') {
                const selectedSet = transactionDataSets[period];
                transactionChart.data.labels = selectedSet.labels;
                transactionChart.data.datasets[0].data = selectedSet.data;
                transactionChart.update();
            } else if (type === 'rev') {
                const selectedSet = revenueDataSets[period];
                revenueChart.data.labels = selectedSet.labels;
                revenueChart.data.datasets[0].data = selectedSet.data;
                revenueChart.update();
            }
        }

        // ==========================================================
        // FITUR REAL-TIME: Update Data di Background Tanpa Refresh
        // ==========================================================
        function fetchRealtimeStats() {
            fetch('/super_admin/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    const pendingEl = document.getElementById('stat-penarikan-pending');
                    if (pendingEl && data.penarikan_pending !== undefined) {
                        pendingEl.innerText = data.penarikan_pending;
                    }
                })
                .catch(error => console.error('Gagal mengambil data real-time:', error));
        }

        // Jalankan pengecekan otomatis setiap 5 detik di latar belakang
        setInterval(fetchRealtimeStats, 5000);
    </script>
@endpush