<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
            font-size: 11px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex { display: flex; justify-content: space-between; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 5px 10px; cursor: pointer;">Cetak Struk</button>
    </div>

    <div class="text-center">
        <h3 style="margin: 0;">{{ $order->merchant->name ?? 'PESANIN' }}</h3>
        <p style="margin: 2px 0; font-size: 9px;">{{ $order->merchant->address ?? '' }}</p>
    </div>

    <div class="line"></div>

    <div class="flex">
        <span>No: {{ $order->order_number }}</span>
    </div>
    <div class="flex">
        <span>Tgl: {{ $order->created_at->format('d/m/Y H:i') }}</span>
    </div>
    <div class="flex">
        <span>Area: {{ $order->qrCode->name ?? '-' }}</span>
    </div>
    <div class="flex">
        <span>Pemesan: {{ $order->customer_name }}</span>
    </div>

    <div class="line"></div>

    @foreach($order->items as $item)
        <div>
            <div>{{ $item->menu->name ?? 'Item' }}</div>
            <div class="flex">
                <span>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</span>
                <span>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @if($item->notes)
                <div style="font-size: 9px; font-style: italic;">* {{ $item->notes }}</div>
            @endif
        </div>
    @endforeach

    <div class="line"></div>

    <div class="flex" style="font-weight: bold;">
        <span>TOTAL:</span>
        <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
    </div>

    <div class="line"></div>

    <div class="text-center" style="margin-top: 10px;">
        <p style="margin: 0;">Terima Kasih!</p>
        <p style="margin: 0; font-size: 9px;">Powered by PesanIn</p>
    </div>

</body>
</html>