<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code - {{ $qrCode->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6">

    <!-- Tombol Cetak / Kembali (Sembunyi saat di-print) -->
    <div class="no-print mb-6 flex gap-3">
        <a href="{{ route('merchant.qr.index') }}" class="px-4 py-2 bg-slate-700 text-white rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-slate-800">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-amber-500 text-slate-950 rounded-xl text-sm font-extrabold flex items-center gap-2 hover:bg-amber-400 shadow-lg shadow-amber-500/20">
            <i class="fa-solid fa-print"></i> Cetak Kartu Meja
        </button>
    </div>

    <!-- Desain Kartu Meja Elegan -->
    <div class="w-80 bg-white rounded-3xl p-8 border-2 border-slate-900 shadow-2xl text-center relative overflow-hidden">
        <!-- Header Kafe -->
        <div class="mb-4">
            <div class="w-12 h-12 bg-amber-500 text-slate-950 rounded-2xl flex items-center justify-center font-black text-xl mx-auto shadow-md mb-2">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <h2 class="font-extrabold text-slate-900 text-lg uppercase tracking-wider">
                {{ $qrCode->merchant->name ?? 'PesanIn Kafe' }}
            </h2>
            <p class="text-xs text-slate-500 font-semibold">Scan QR untuk Pesan & Bayar</p>
        </div>

        <!-- Nama Meja -->
        <div class="my-4 bg-slate-900 text-amber-400 py-2 rounded-xl font-black text-xl tracking-widest uppercase">
            {{ $qrCode->name }}
        </div>

        <!-- QR Code -->
        <div class="bg-amber-50 p-4 rounded-2xl border-2 border-dashed border-amber-300 inline-block my-2">
            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate(route('customer.menu', $qrCode->code_hash)) !!}
        </div>

        <!-- Petunjuk Singkat -->
        <div class="mt-4 pt-4 border-t border-slate-100 text-slate-500 text-[11px] font-medium space-y-1">
            <p><i class="fa-solid fa-camera text-amber-500 mr-1"></i> Buka Kamera HP kamu</p>
            <p><i class="fa-solid fa-qrcode text-amber-500 mr-1"></i> Arahkan ke QR Code di atas</p>
            <p><i class="fa-solid fa-utensils text-amber-500 mr-1"></i> Pilih menu dan lakukan pembayaran</p>
        </div>

        <!-- Footer Powered By -->
        <div class="mt-6 text-[9px] font-bold text-slate-400 tracking-widest uppercase">
            Powered by PesanIn
        </div>
    </div>

</body>
</html>