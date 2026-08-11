<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-slate-800 rounded-3xl p-6 border border-slate-700/80 shadow-2xl text-center space-y-6">
        
        <!-- Icon Berhasil -->
        <div class="w-20 h-20 bg-amber-500/20 text-amber-400 rounded-full flex items-center justify-center text-3xl mx-auto border border-amber-500/30 animate-pulse">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <div>
            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/20 uppercase tracking-widest">
                Pesanan Berhasil Dikirim
            </span>
            <h1 class="text-2xl font-black text-white mt-2">Terima Kasih, {{ $order->customer_name }}!</h1>
            <p class="text-xs text-slate-400 mt-1">Pesanan kamu sedang disiapkan oleh tim dapur.</p>
        </div>

        <!-- Detail Rincian Pesanan -->
        <div class="bg-slate-900/80 rounded-2xl p-4 text-left border border-slate-700/50 space-y-3">
            <div class="flex justify-between text-xs pb-2 border-b border-slate-800">
                <span class="text-slate-400">No. Order:</span>
                <span class="font-extrabold text-amber-400">#{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between text-xs pb-2 border-b border-slate-800">
                <span class="text-slate-400">Lokasi / Meja:</span>
                <span class="font-bold text-white">{{ $order->qrCode->name ?? 'Meja' }}</span>
            </div>

            <!-- List Makanan -->
            <div class="space-y-2 pt-1">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-300 font-semibold">{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu' }}</span>
                        <span class="text-slate-400">Rp {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-800 pt-3 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-300">Total Tagihan:</span>
                <span class="text-base font-black text-amber-400">Rp {{ number_format($order->total_amount ?? $order->total_price ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tampilan Pembayaran QRIS -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-amber-500/30 text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500 text-slate-950 text-[10px] font-black rounded-full uppercase tracking-wider">
                <i class="fa-solid fa-qrcode"></i> Scan & Bayar via QRIS
            </div>
            
            <!-- Kode QRIS Generator -->
            <div class="p-3 bg-white rounded-2xl inline-block shadow-lg">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=PAY-{{ $order->order_number }}-RP-{{ $order->total_amount }}" 
                     alt="QRIS Pembayaran" class="w-44 h-44 mx-auto rounded-lg">
            </div>

            <div class="space-y-1">
                <p class="text-xs text-slate-300 font-bold">
                    Scan menggunakan <span class="text-amber-400">GoPay, OVO, Dana, ShopeePay,</span> atau <span class="text-amber-400">M-Banking</span>.
                </p>
                <p class="text-[10px] text-slate-400">
                    Tunjukkan bukti transfer ke staf kafe jika diperlukan.
                </p>
            </div>
        </div>

        <!-- Tombol Pesan Lagi -->
        <div>
            <a href="{{ route('customer.menu', $order->qrCode->code_hash) }}" 
               class="block w-full bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 rounded-xl text-xs transition">
                + Tambah Pesanan Lain
            </a>
        </div>

    </div>

</body>
</html>