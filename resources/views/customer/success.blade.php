<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-2xl shadow-md w-full max-w-md text-center space-y-4">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-3xl">
            ✓
        </div>
        <h2 class="text-xl font-bold text-gray-800">Pesanan Berhasil Dibuat!</h2>
        <p class="text-xs text-gray-500">Nomor Pesanan: <span class="font-mono font-bold text-gray-700">{{ $order->order_number }}</span></p>

        <div class="bg-gray-50 p-4 rounded-xl text-left text-xs space-y-2 border">
            <p><strong>Merchant:</strong> {{ $order->merchant->name }}</p>
            <p><strong>Lokasi:</strong> {{ $order->qrCode->name }}</p>
            <p><strong>Pemesan:</strong> {{ $order->customer_name }}</p>
            <p><strong>Status:</strong> <span class="uppercase font-bold text-yellow-600">{{ $order->status }}</span></p>
            <hr>
            <div class="space-y-1">
                @foreach($order->items as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->menu->name }} (x{{ $item->quantity }})</span>
                        <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <hr>
            <div class="flex justify-between text-sm font-bold text-gray-800 pt-1">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <p class="text-xs text-gray-400">Silakan tunjukkan halaman ini ke kasir atau tunggu pesanan Anda diproses.</p>
    </div>
</body>
</html>