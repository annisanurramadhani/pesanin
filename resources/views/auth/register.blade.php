@extends('layouts.app')

@section('body')
    <div class="min-h-screen bg-slate-50">

        {{-- Header --}}
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">

                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">
                        PesanIn
                    </h1>

                    <p class="text-xs text-slate-500">
                        Solusi digital untuk bisnis Anda
                    </p>
                </div>

                <a href="{{ route('login') }}"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                    Masuk
                </a>

            </div>
        </header>


        {{-- Content --}}
        <main class="px-6 pb-20 pt-14">

            <div class="mx-auto max-w-2xl">

                {{-- Back --}}
                <a href="{{ url()->previous() }}"
                    class="mb-8 inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-slate-900">

                    <i class="fa-solid fa-arrow-left"></i>

                    Kembali

                </a>


                {{-- Heading --}}
                <div class="mb-10">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">

                        Data Pemilik

                    </span>

                    <h2 class="mt-4 text-4xl font-black tracking-tight text-slate-900">
                        Buat Akun PesanIn
                    </h2>

                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-500">
                        Daftarkan akun Anda untuk melanjutkan proses berlangganan PesanIn.
                    </p>

                </div>


                {{-- Register Card --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    {{-- Card Header --}}
                    <div class="border-b border-slate-200 px-6 py-5 sm:px-8">

                        <div class="flex items-center gap-4">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-500">

                                <i class="fa-solid fa-user-plus text-lg"></i>

                            </div>

                            <div>

                                <h3 class="text-lg font-extrabold text-slate-900">
                                    Informasi Akun
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    Gunakan data yang valid dan email yang aktif.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Form --}}
                    <div class="p-6 sm:p-8">

                        <form method="POST" action="{{ route('register') }}" class="space-y-5">

                            @csrf
                            @if ($encryptedDuration)
                                <input type="hidden" name="duration" value="{{ $encryptedDuration }}">
                            @endif


                            {{-- Name --}}
                            <div>

                                <label for="name"
                                    class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                    Nama Lengkap

                                </label>

                                <div class="relative">

                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                                        <i class="fa-solid fa-user"></i>

                                    </div>

                                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                        autofocus autocomplete="name" placeholder="Masukkan nama lengkap"
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">

                                </div>

                                <x-input-error :messages="$errors->get('name')" class="mt-2" />

                            </div>


                            {{-- Email --}}
                            <div>

                                <label for="email"
                                    class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                    Email Aktif

                                </label>

                                <div class="relative">

                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                                        <i class="fa-solid fa-envelope"></i>

                                    </div>

                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="username" placeholder="nama@email.com"
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-4 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">

                                </div>

                                <p class="mt-2 text-xs text-slate-400">
                                    Email akan digunakan untuk proses verifikasi akun.
                                </p>

                                <x-input-error :messages="$errors->get('email')" class="mt-2" />

                            </div>


                            {{-- Password --}}
                            <div>

                                <label for="password"
                                    class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                    Password

                                </label>

                                <div class="relative">

                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                                        <i class="fa-solid fa-lock"></i>

                                    </div>

                                    <input id="password" type="password" name="password" required
                                        autocomplete="new-password" placeholder="Masukkan password"
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-12 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">

                                    <button type="button" onclick="togglePassword('password', 'passwordIcon')"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-slate-700">

                                        <i id="passwordIcon" class="fa-solid fa-eye"></i>

                                    </button>

                                </div>

                                <p class="mt-2 text-xs text-slate-400">
                                    Minimal 8 karakter, terdiri dari huruf, angka, dan simbol.
                                </p>

                                <x-input-error :messages="$errors->get('password')" class="mt-2" />

                            </div>


                            {{-- Confirm Password --}}
                            <div>

                                <label for="password_confirmation"
                                    class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-700">

                                    Konfirmasi Password

                                </label>

                                <div class="relative">

                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">

                                        <i class="fa-solid fa-shield-halved"></i>

                                    </div>

                                    <input id="password_confirmation" type="password" name="password_confirmation" required
                                        autocomplete="new-password" placeholder="Ulangi password"
                                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pl-11 pr-12 text-sm font-medium text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:ring-2 focus:ring-amber-500/10">

                                    <button type="button"
                                        onclick="togglePassword('password_confirmation', 'confirmationIcon')"
                                        class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-slate-700">

                                        <i id="confirmationIcon" class="fa-solid fa-eye"></i>

                                    </button>

                                </div>

                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

                            </div>


                            {{-- Submit --}}
                            <div class="pt-3">

                                <button type="submit"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                    <span>
                                        Buat Akun
                                    </span>

                                    <i class="fa-solid fa-arrow-right"></i>

                                </button>

                            </div>


                            {{-- Login --}}
                            <div class="border-t border-slate-100 pt-5 text-center">

                                <p class="text-sm text-slate-500">

                                    Sudah punya akun?

                                    <a href="{{ route('login') }}"
                                        class="font-extrabold text-amber-600 transition hover:text-amber-500">

                                        Login sekarang

                                    </a>

                                </p>

                            </div>

                        </form>

                    </div>

                </div>


                {{-- Footer Info --}}
                <div class="mt-6 flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-4">

                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500"></i>

                    <p class="text-xs leading-5 text-slate-500">
                        Pastikan email yang Anda gunakan aktif karena akan digunakan
                        untuk proses verifikasi akun sebelum melanjutkan langganan.
                    </p>

                </div>

            </div>

        </main>

    </div>


    {{-- Password Toggle --}}
    <script>
        function togglePassword(inputId, iconId) {

            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                input.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

            }

        }
    </script>
@endsection
