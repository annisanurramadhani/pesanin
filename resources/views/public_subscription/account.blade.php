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

                <a href="{{ route('public.subscription.show', $duration->package->slug) }}"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">

                    Kembali

                </a>

            </div>
        </header>


        {{-- Content --}}
        <main class="flex min-h-[calc(100vh-81px)] items-center justify-center px-6 py-16">

            <div class="w-full max-w-4xl">

                {{-- Heading --}}
                <div class="text-center">

                    <span
                        class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wider text-amber-600">

                        Konfirmasi Pesanan

                    </span>

                    <h2 class="mt-5 text-3xl font-black tracking-tight text-slate-900 md:text-4xl">

                        Sudah punya akun PesanIn?

                    </h2>

                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-500">

                        Pilih opsi di bawah untuk melanjutkan proses berlangganan.

                    </p>

                </div>


                {{-- Account Options --}}
                <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2">


                    {{-- Sudah Punya Akun --}}
                    <a href="{{ route('login', ['duration' => encryptId($duration->id)]) }}"
                        class="group rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-amber-300 hover:shadow-xl">

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-500 transition duration-300 group-hover:bg-amber-500 group-hover:text-slate-950">

                            <i class="fa-solid fa-right-to-bracket text-xl"></i>

                        </div>

                        <h3 class="mt-6 text-xl font-black text-slate-900">
                            Ya, Saya Sudah Punya Akun
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            Saya sudah memiliki akun PesanIn dan ingin melanjutkan
                            menggunakan akun yang sudah ada.
                        </p>

                        <div class="mt-7 flex items-center gap-2 text-sm font-extrabold text-amber-600">

                            <span>
                                Login Sekarang
                            </span>

                            <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i>

                        </div>

                    </a>


                    {{-- Belum Punya Akun --}}
                    <a href="{{ route('register', ['duration' => encryptId($duration->id)]) }}"
                        class="group rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-amber-300 hover:shadow-xl">

                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 transition duration-300 group-hover:bg-amber-500 group-hover:text-slate-950">

                            <i class="fa-solid fa-user-plus text-xl"></i>

                        </div>

                        <h3 class="mt-6 text-xl font-black text-slate-900">
                            Belum Punya Akun
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            Saya belum memiliki akun PesanIn dan ingin membuat
                            akun baru untuk melanjutkan langganan.
                        </p>

                        <div class="mt-7 flex items-center gap-2 text-sm font-extrabold text-slate-700">

                            <span>
                                Daftar Sekarang
                            </span>

                            <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i>

                        </div>

                    </a>

                </div>

            </div>

        </main>

    </div>

@endsection
