<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'PesanIn') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-[#f3f4f8]">

    <div class="min-h-screen flex items-center justify-center px-5 py-10">

        <div class="w-full max-w-md">

            <div class="mb-7 text-center">

                <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-2xl shadow-lg shadow-amber-500/30">
                    <i class="fa-solid fa-mug-hot"></i>
                </div>

                <h1 class="text-2xl font-extrabold text-[#111827] tracking-tight">
                    PesanIn
                </h1>

                <p class="mt-1 text-[11px] text-slate-500 tracking-widest uppercase font-bold">
                    PesanIn Dashboard
                </p>

            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-900/5 px-7 py-8 sm:px-8 sm:py-9">

                {{ $slot }}

            </div>

            <p class="mt-5 text-center text-xs text-slate-400 font-medium">
                © {{ date('Y') }} PesanIn
            </p>

        </div>

    </div>

</body>
</html>
