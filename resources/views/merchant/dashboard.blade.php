@extends('layouts.merchant')

@section('header')

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    <div>
        <h2 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900">
            Dashboard Merchant
        </h2>

        <p class="mt-1 text-xs font-medium text-slate-500">
            Pantau aktivitas penjualan, pesanan masuk, dan performa kafemu secara real-time.
        </p>
    </div>

    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200/60 bg-emerald-50 px-3.5 py-1.5 text-xs font-bold text-emerald-600">
            <span class="h-2 w-2 animate-ping rounded-full bg-emerald-500"></span>
            Sistem Pesanan On
        </div>
    </div>

</div>

@endsection


@section('content')

<div class="space-y-8">

    {{-- ================================================================
         STAT CARDS
    ================================================================= --}}

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

        {{-- Total Menu --}}
        <div class="group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md">
            <div class="absolute -bottom-4 -right-4 h-24 w-24 rounded-full bg-amber-500/10 transition duration-300 group-hover:scale-125"></div>

            <div class="relative z-10 space-y-1">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Total Menu
                </p>

                <h3 class="text-4xl font-black tracking-tight text-slate-900">
                    {{ $totalMenu ?? 0 }}
                </h3>

                <p class="pt-1 text-xs font-medium text-slate-500">
                    Produk siap dipesan
                </p>
            </div>

            <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500 text-xl font-black text-slate-950 shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-utensils"></i>
            </div>
        </div>


        {{-- Pesanan Hari Ini --}}
        <div class="group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md">
            <div class="absolute -bottom-4 -right-4 h-24 w-24 rounded-full bg-emerald-500/10 transition duration-300 group-hover:scale-125"></div>

            <div class="relative z-10 space-y-1">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Pesanan Hari Ini
                </p>

                <h3 class="text-4xl font-black tracking-tight text-slate-900">
                    {{ $todayOrdersCount ?? $todayOrders ?? 0 }}
                </h3>

                <p class="pt-1 text-xs font-medium text-slate-500">
                    Total transaksi hari ini
                </p>
            </div>

            <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-500 text-xl font-black text-white shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
        </div>


        {{-- Total Pendapatan Hari Ini --}}
        <div class="group relative flex items-center justify-between overflow-hidden rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-200 hover:shadow-md">
            <div class="absolute -bottom-4 -right-4 h-24 w-24 rounded-full bg-blue-500/10 transition duration-300 group-hover:scale-125"></div>

            <div class="relative z-10 space-y-1">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    Total Pendapatan Hari Ini
                </p>

                <h3 class="text-3xl font-black tracking-tight text-emerald-600">
                    Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}
                </h3>

                <p class="pt-1 text-xs font-medium text-slate-500">
                    Rekap pemasukan hari ini
                </p>
            </div>

            <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-xl font-black text-white shadow-lg shadow-blue-500/20">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

    </div>


    {{-- ================================================================
         TABEL PESANAN TERBARU
    ================================================================= --}}

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-100 p-6">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">
                    Pesanan Terbaru Masuk
                </h3>

                <p class="mt-0.5 text-xs text-slate-400">
                    5 transaksi paling akhir dari pelanggan meja
                </p>
            </div>

            <a href="{{ route('merchant.orders.index') }}" class="flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 hover:shadow">
                <span>Lihat Semua Orders</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>


        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="p-4 pl-6">No. Order</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Pesanan</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">Total</th>
                        <th class="p-4 text-center">Diselesaikan</th>
                        <th class="p-4 pr-6 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders ?? [] as $order)
                        @php
                            $itemTotal = $order->items->sum(function ($item) {
                                return $item->subtotal ?? ($item->price * $item->quantity);
                            });

                            $grandTotal = ($order->total_amount > 0)
                                ? $order->total_amount
                                : (($order->total_price > 0) ? $order->total_price : $itemTotal);

                            $isCash = in_array(strtolower($order->payment_method), ['cash', 'kasir', 'tunai']);
                            $statusStr = strtolower($order->status ?? '');
                        @endphp

                        <tr class="transition hover:bg-slate-50/60">

                            {{-- No. Order --}}
                            <td class="p-4 pl-6">
                                <span class="block text-base font-black text-slate-900">
                                    #{{ $order->order_number }}
                                </span>

                                <span class="mt-1 inline-flex items-center gap-1 rounded-md border border-amber-200/60 bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-600">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $order->qrCode->name ?? 'Meja General' }}
                                </span>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="p-4">
                                <p class="font-extrabold text-slate-800">
                                    {{ $order->customer_name }}
                                </p>
                            </td>

                            {{-- Pesanan --}}
                            <td class="p-4">
                                <div class="space-y-1">
                                    @foreach($order->items as $item)
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            <span class="font-semibold text-slate-700">{{ $item->menu_name ?? $item->menu->name ?? 'Menu' }}</span>
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-black text-slate-900">(x{{ $item->quantity }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Metode Bayar --}}
                            <td class="p-4">
                                @if($isCash)
                                    <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200/80 bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-800">
                                        <i class="fa-solid fa-cash-register text-slate-500"></i> Bayar Kasir
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200/80 bg-amber-50 px-3 py-1 text-xs font-extrabold text-amber-700">
                                        <i class="fa-solid fa-qrcode text-amber-600"></i> QRIS
                                    </span>
                                @endif
                            </td>

                            {{-- Total --}}
                            <td class="p-4">
                                <span class="text-base font-black text-slate-900">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Diselesaikan --}}
                            <td class="p-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    @if(in_array($statusStr, ['selesai', 'completed']))
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-emerald-200/80 bg-emerald-50 px-2.5 py-1 text-xs font-black text-emerald-700">
                                            <i class="fa-solid fa-check text-[10px]"></i> Selesai
                                        </span>
                                    @elseif(in_array($statusStr, ['batal', 'cancelled']))
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-rose-200/80 bg-rose-50 px-2.5 py-1 text-xs font-black text-rose-700">
                                            <i class="fa-solid fa-xmark text-[10px]"></i> Dibatalkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-lg border border-amber-200/80 bg-amber-50 px-2.5 py-1 text-xs font-black text-amber-700">
                                            <i class="fa-regular fa-clock text-[10px]"></i> Diproses
                                        </span>
                                    @endif

                                    <span class="text-[11px] font-bold text-slate-500">
                                        {{ $order->created_at->format('d/m/Y H:i') }} WIB
                                    </span>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="p-4 pr-6 text-center">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.outside="open = false" type="button" 
                                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 focus:outline-none cursor-pointer">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 z-50 mt-2 w-40 origin-top-right rounded-2xl border border-slate-100 bg-white py-1.5 shadow-xl focus:outline-none">
                                        
                                        <a href="{{ route('merchant.orders.receipt', encryptId($order->id)) }}" target="_blank"
                                            class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                            <i class="fa-solid fa-print w-4 text-amber-500"></i> Cetak Struk
                                        </a>

                                        <a href="#"
                                            class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                                            <i class="fa-solid fa-pen-to-square w-4 text-blue-500"></i> Edit Pesanan
                                        </a>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <form action="#" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="alert('Fitur hapus belum diaktifkan')"
                                                class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-xs font-bold text-rose-600 transition hover:bg-rose-50 cursor-pointer">
                                                <i class="fa-solid fa-trash-can w-4"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="p-12 text-center">
                                <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-xl text-slate-400">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>

                                <p class="text-sm font-bold text-slate-700">
                                    Belum ada pesanan masuk
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Setiap pesanan baru dari scan QR pelanggan akan muncul di sini secara langsung.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

</div>


{{-- ================================================================
     SUBSCRIPTION EXPIRED MODAL
================================================================ --}}

@if (($showRenewalModal ?? false) === true)

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @php
        $hasRenewalSelection =
            !empty($renewalPackage)
            && !empty($renewalDuration)
            && session('subscription.from_public') === true;

        $subscriptionIndexUrl = route('public.subscription.index');
        $continuePaymentUrl = route('public.subscription.account.continue');

        $renewalPackageName = $renewalPackage->name ?? '';
        $renewalDurationName = $renewalDuration->name ?? '';
        $renewalDurationDays = $renewalDuration->duration_days ?? 0;

        $renewalPrice = $renewalDuration
            ? number_format($renewalDuration->discount_price ?? $renewalDuration->price, 0, ',', '.')
            : '0';
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hasRenewalSelection = @json($hasRenewalSelection);
            const subscriptionIndexUrl = @json($subscriptionIndexUrl);
            const continuePaymentUrl = @json($continuePaymentUrl);
            const packageName = @json($renewalPackageName);
            const durationName = @json($renewalDurationName);
            const durationDays = @json($renewalDurationDays);
            const price = @json($renewalPrice);

            if (hasRenewalSelection) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Langganan Anda Telah Berakhir',
                    html:
                        '<div style="text-align:left;">' +
                            '<p style="font-size:14px;color:#64748b;line-height:1.6;margin-bottom:16px;">' +
                                'Langganan Anda telah berakhir. Pilihan paket dan durasi yang sebelumnya dipilih masih tersimpan.' +
                            '</p>' +
                            '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:16px;">' +
                                '<p style="font-size:11px;font-weight:800;text-transform:uppercase;color:#94a3b8;margin:0;">Paket Dipilih</p>' +
                                '<p style="font-size:18px;font-weight:900;color:#0f172a;margin:5px 0 0;">' + packageName + '</p>' +
                                '<p style="font-size:13px;font-weight:600;color:#64748b;margin:5px 0 0;">' + durationName + ' · ' + durationDays + ' hari</p>' +
                                '<p style="font-size:20px;font-weight:900;color:#f59e0b;margin:10px 0 0;">Rp ' + price + '</p>' +
                            '</div>' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan Pembayaran',
                    cancelButtonText: 'Pilih Paket Lain',
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#0f172a',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        window.location.replace(continuePaymentUrl);
                        return;
                    }
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.replace(subscriptionIndexUrl);
                    }
                });
                return;
            }

            Swal.fire({
                icon: 'warning',
                title: 'Langganan Anda Telah Berakhir',
                text: 'Masa berlangganan Anda telah berakhir. Silakan pilih paket untuk memperpanjang langganan.',
                confirmButtonText: 'Perpanjang Langganan',
                confirmButtonColor: '#f59e0b',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    window.location.replace(subscriptionIndexUrl);
                }
            });
        });
    </script>

@endif

@endsection