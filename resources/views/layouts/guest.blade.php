<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'PesanIn') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

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

                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-lg shadow-slate-900/10 overflow-hidden">
                    <img
                        src="{{ asset('assets/images/logo-login.png') }}"
                        alt="PesanIn"
                        class="h-full w-full object-contain p-1"
                    >
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

            {{-- ======================================================
                FOOTER
            ======================================================= --}}
            <footer class="border-t border-slate-200 pt-8 pb-2 text-center">

                <p class="text-xs text-slate-400">
                    © {{ date('Y') }} PesanIn. Semua hak dilindungi.
                </p>
            </footer>

        </div>

    </div>


    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- Success --}}
    @if (session('success'))

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: @json(session('success')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#f59e0b',
                    background: '#ffffff',
                    color: '#111827',

                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                    }
                });

            });
        </script>

    @endif


    {{-- Error --}}
    @if (session('error'))

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: @json(session('error')),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#111827',
                    background: '#ffffff',
                    color: '#111827',

                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                    }
                });

            });
        </script>

    @endif


    {{-- Validation Error --}}
    @if ($errors->any())

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    text: @json($errors->first()),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#111827',
                    background: '#ffffff',
                    color: '#111827',

                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                    }
                });

            });
        </script>

    @endif

</body>

</html>
