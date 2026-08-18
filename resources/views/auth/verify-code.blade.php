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

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                        Keluar
                    </button>
                </form>

            </div>
        </header>


        {{-- Content --}}
        <main class="flex min-h-[calc(100vh-81px)] items-center justify-center px-6 py-16">

            <div class="w-full max-w-md">

                {{-- Heading --}}
                <div class="text-center">

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-amber-500">
                        <i class="fa-solid fa-envelope text-2xl"></i>
                    </div>

                    <span
                        class="mt-6 inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">
                        Verifikasi Email
                    </span>

                    <h2 class="mt-5 text-3xl font-black tracking-tight text-slate-900">
                        Masukkan Kode Verifikasi
                    </h2>

                    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-500">
                        Kami telah mengirimkan kode verifikasi 6 digit ke email
                        <span class="font-bold text-slate-700">
                            {{ auth()->user()->email }}
                        </span>.
                    </p>

                </div>


                {{-- Card --}}
                <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">

                    {{-- Success --}}
                    @if (session('success'))

                        <div
                            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                            {{ session('success') }}
                        </div>

                    @endif


                    {{-- Form OTP --}}
                    <form method="POST" action="{{ route('verification.code.verify') }}">

                        @csrf

                        <div>

                            <label
                                for="code"
                                class="block text-sm font-bold text-slate-700">
                                Kode Verifikasi
                            </label>

                            <input
                                id="code"
                                type="text"
                                name="code"
                                maxlength="6"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                autofocus
                                required
                                class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-4 text-center text-2xl font-black tracking-[0.5em] text-slate-900 outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
                                placeholder="000000">

                            @error('code')
                                <p class="mt-2 text-sm font-semibold text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <button
                            type="submit"
                            class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                            <i class="fa-solid fa-circle-check"></i>

                            Verifikasi Email

                        </button>

                    </form>


                    {{-- Resend --}}
                    <div class="mt-6 border-t border-slate-100 pt-6 text-center">

                        <p class="text-sm text-slate-500">
                            Belum menerima kode?
                        </p>

                        <form
                            method="POST"
                            action="{{ route('verification.code.resend') }}"
                            class="mt-2">

                            @csrf

                            <button
                                type="submit"
                                class="text-sm font-extrabold text-amber-600 transition hover:text-amber-500">

                                Kirim Ulang Kode

                            </button>

                        </form>

                        <p class="mt-3 text-xs text-slate-400">
                            Kode berlaku selama 10 menit.
                        </p>

                    </div>

                </div>

            </div>

        </main>

    </div>

@endsection
