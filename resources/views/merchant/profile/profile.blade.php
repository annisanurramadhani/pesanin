@extends('layouts.app')

@section('body')

    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">

                <div>

                    <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                        Pengaturan Profil Kafe
                    </h2>

                    <p class="text-xs font-medium text-slate-500 mt-1">
                        Kelola informasi nama, kontak, dan alamat kafe kamu.
                    </p>

                </div>

            </header>


            {{-- Content --}}
            <main class="flex-1 p-8">

                <div class="max-w-4xl mx-auto">

                    {{-- Notifikasi --}}
                    @if (session('success'))

                        <div
                            class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 shadow-sm">

                            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>

                            <span>
                                {{ session('success') }}
                            </span>

                        </div>

                    @endif


                    {{-- Form --}}
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-slate-100">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shadow-md shadow-amber-500/20">

                                    <i class="fa-solid fa-store"></i>

                                </div>

                                <div>

                                    <h3 class="font-extrabold text-slate-900">
                                        Informasi Kafe
                                    </h3>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Perbarui informasi yang digunakan untuk profil tokomu.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="p-6">

                            <form
                                action="{{ route('merchant.profile-kafe.update') }}"
                                method="POST"
                                class="space-y-6">

                                @csrf
                                @method('PUT')


                                {{-- Nama Kafe --}}
                                <div>

                                    <label
                                        for="name"
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">

                                        Nama Kafe / Resto

                                    </label>

                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        value="{{ old('name', $merchant->name ?? '') }}"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition"
                                        required
                                        autofocus>

                                    @error('name')

                                        <p class="mt-1 text-xs font-semibold text-rose-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- Nomor HP --}}
                                <div>

                                    <label
                                        for="phone"
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">

                                        Nomor HP / WhatsApp

                                    </label>

                                    <input
                                        id="phone"
                                        name="phone"
                                        type="text"
                                        value="{{ old('phone', $merchant->phone ?? '') }}"
                                        placeholder="08123456789"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition">

                                    @error('phone')

                                        <p class="mt-1 text-xs font-semibold text-rose-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- Alamat --}}
                                <div>

                                    <label
                                        for="address"
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">

                                        Alamat Lengkap Kafe

                                    </label>

                                    <textarea
                                        id="address"
                                        name="address"
                                        rows="4"
                                        placeholder="Jl. Veteran No. 10, Purwakarta"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition">{{ old('address', $merchant->address ?? '') }}</textarea>

                                    @error('address')

                                        <p class="mt-1 text-xs font-semibold text-rose-500">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>


                                {{-- Button --}}
                                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                                    <a
                                        href="{{ route('merchant.dashboard') }}"
                                        class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">

                                        Batal

                                    </a>

                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">

                                        <i class="fa-solid fa-floppy-disk"></i>

                                        Simpan Perubahan

                                    </button>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </main>

        </div>

    </div>

@endsection
