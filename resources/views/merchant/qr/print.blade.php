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
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            a::after {
                content: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6">

    <!-- Tombol Cetak / Kembali -->
    <div class="no-print mb-6 flex gap-3">
        <a href="{{ route('merchant.qr.index') }}" class="px-4 py-2 bg-slate-700 text-white rounded-xl text-sm font-bold flex items-center gap-2 hover:bg-slate-800">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-amber-500 text-slate-950 rounded-xl text-sm font-extrabold flex items-center gap-2 hover:bg-amber-400 shadow-lg shadow-amber-500/20">
            <i class="fa-solid fa-print"></i> Cetak Kartu Meja
        </button>
    </div>

    <!-- Desain Kartu Meja -->
    <div class="w-[450px] bg-white rounded-3xl p-8 border-2 border-slate-900 shadow-xl text-center">
        
        <!-- Nama Meja -->
        <div class="my-4 bg-slate-900 text-amber-400 py-4 rounded-xl font-black text-3xl tracking-widest uppercase">
            {{ $qrCode->name }}
        </div>

        <!-- QR Code (Diubah Menggunakan API Gambar) -->
        <div class="bg-white p-3 rounded-xl inline-block mt-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=340x340&data={{ urlencode(route('customer.menu', $qrCode->code)) }}" 
                alt="QR Code {{ $qrCode->name }}" 
                class="w-80 h-80 mx-auto">
        </div>
    </div>

</body>
</html>