<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm; /* Standar Ukuran Kertas Thermal Printer */
            margin: 0 auto;
            padding: 10px;
            font-size: 11px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex-between { display: flex; justify-content: space-between; }
        .no-print { margin-bottom: 15px; }
        @media print {
            .no-print { display: none; }
            body { width: 100%; padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak -->
    <div class="no-print text-center">
        <button onclick="window.print()" style="padding: 6px 12px; background: #000; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Struk
        </button>
    </div>

    <!-- Header Struk -->
    <div class="text-center">
        <h3 class="font-bold" style="margin: 0; text-transform: uppercase;">{{ $order->merchant->name ?? 'PESANIN KAFE' }}</h3>
        <p style="margin: 2px 0;">Struk Pembayaran</p>
    </div>

    <div class="line"></div>

    <!-- Info Pesanan -->
    <div>
        <p style="margin: 2px 0;">No: #{{ $order->order_number }}</p>

        <p style="margin: 2px 0;">Meja: {{ $order->qrCode->name ?? 'Takeaway' }}</p>
        <p style="margin: 2px 0;">Pemesan: {{ $order->customer_name }}</p>
        <p style="margin: 2px 0;">Waktu: {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="line"></div>

    <!-- Daftar Item -->
    @foreach($order->items as $item)
        <div style="margin-bottom: 4px;">
            <div class="font-bold">{{ $item->menu->name ?? 'Menu' }}</div>
            <div class="flex-between">
                <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    @endforeach

    <div class="line"></div>

    <!-- Total -->
    <div class="flex-between font-bold" style="font-size: 13px;">
        <span>TOTAL:</span>
        <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
    </div>

    <div class="line"></div>

    <!-- Footer -->
    <div class="text-center" style="margin-top: 10px;">
        <p style="margin: 2px 0;">Terima Kasih atas Kunjungan Anda!</p>
        <p style="margin: 2px 0; font-size: 9px; color: #555;">Powered by PesanIn</p>
    </div>

</body>
</html>