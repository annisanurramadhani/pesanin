@extends('layouts.app')

@section('body')

    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>
                        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                            Kelola QR Code Meja
                        </h2>

                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Generate QR Code unik untuk ditempel pada meja kafe atau area pemesanan.
                        </p>
                    </div>

                </div>

            </header>


            {{-- Content --}}
            <main class="flex-1 p-8">

                <div class="space-y-8">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        {{-- Form Tambah QR --}}
                        <div
                            class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">

                            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">

                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shadow-md shadow-amber-500/20">

                                    <i class="fa-solid fa-qrcode"></i>

                                </div>

                                <div>

                                    <h3 class="font-extrabold text-slate-900 text-base">
                                        Buat QR Baru
                                    </h3>

                                    <p class="text-[11px] font-medium text-slate-400">
                                        Generate link & QR meja
                                    </p>

                                </div>

                            </div>


                            <form action="{{ route('merchant.qr.store') }}" method="POST" class="space-y-5">

                                @csrf

                                <div>

                                    <label
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">

                                        Nama QR / Nomor Meja *

                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                        placeholder="Contoh: Meja 03, VIP Outdoor"
                                        required>

                                    @error('name')
                                        <p class="mt-1 text-xs font-semibold text-rose-500">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <div>

                                    <label
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">

                                        Jenis Area *

                                    </label>

                                    <select
                                        name="type"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition cursor-pointer"
                                        required>

                                        <option value="dine_in" {{ old('type') === 'dine_in' ? 'selected' : '' }}>
                                            Dine In
                                        </option>

                                        <option value="takeaway" {{ old('type') === 'takeaway' ? 'selected' : '' }}>
                                            Take Away
                                        </option>

                                    </select>

                                    @error('type')
                                        <p class="mt-1 text-xs font-semibold text-rose-500">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <button
                                    type="submit"
                                    class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-4 px-4 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-amber-500/25 active:scale-[0.98] flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-plus"></i>

                                    <span>
                                        Generate QR Code Sekarang
                                    </span>

                                </button>

                            </form>

                        </div>


                        {{-- Daftar QR --}}
                        <div
                            class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

                            <div class="p-6 border-b border-slate-100 flex items-center justify-between">

                                <div>

                                    <h3 class="font-extrabold text-slate-900 text-lg">
                                        Daftar QR Code Aktif
                                    </h3>

                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Semua meja terdaftar yang siap dipindai oleh pelanggan
                                    </p>

                                </div>

                                <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold">
                                    Total: {{ count($qrCodes) }} QR
                                </span>

                            </div>


                            <div class="overflow-x-auto">

                                <table class="w-full text-sm text-left">

                                    <thead
                                        class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">

                                        <tr>

                                            <th class="p-4 pl-6">
                                                Nama / Label Meja
                                            </th>

                                            <th class="p-4">
                                                Jenis Area
                                            </th>

                                            <th class="p-4">
                                                URL Pesanan
                                            </th>

                                            <th class="p-4">
                                                Status
                                            </th>

                                            <th class="p-4 pr-6 text-center">
                                                Aksi
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody class="divide-y divide-slate-100">

                                        @forelse ($qrCodes as $qr)

                                            <tr class="hover:bg-slate-50/60 transition">

                                                <td class="p-4 pl-6">

                                                    <div class="flex items-center gap-3">

                                                        <div
                                                            class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">

                                                            <i class="fa-solid fa-location-dot"></i>

                                                        </div>

                                                        <span class="font-extrabold text-slate-900 text-base">
                                                            {{ $qr->name }}
                                                        </span>

                                                    </div>

                                                </td>


                                                <td class="p-4">

                                                    @if (strtolower($qr->type) === 'takeaway')

                                                        <span
                                                            class="px-3 py-1 bg-orange-50 text-orange-700 border border-orange-200/80 font-extrabold rounded-lg text-xs inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-bag-shopping text-[10px]"></i>
                                                            Take Away
                                                        </span>

                                                    @else

                                                        <span
                                                            class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200/80 font-extrabold rounded-lg text-xs inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-utensils text-[10px]"></i>
                                                            Dine In
                                                        </span>

                                                    @endif

                                                </td>


                                                <td class="p-4">

                                                    <a
                                                        href="{{ route('customer.menu', $qr->code) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-2 text-xs font-bold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 px-3.5 py-2 rounded-xl transition">

                                                        <span>
                                                            Buka Link Pesanan
                                                        </span>

                                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>

                                                    </a>

                                                </td>


                                                <td class="p-4">

                                                    <span
                                                        class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 font-extrabold rounded-xl border border-emerald-200/80 inline-flex items-center gap-1.5">

                                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>

                                                        Aktif

                                                    </span>

                                                </td>


                                                <td class="p-4 pr-6 text-center">

                                                    <div class="flex items-center justify-center gap-2">

                                                        <a
                                                            href="{{ route('merchant.qr.print', encrypt($qr->id)) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 text-amber-700 hover:bg-amber-500 hover:text-slate-950 rounded-xl text-xs font-extrabold transition border border-amber-500/20"
                                                            title="Cetak Kartu Meja">

                                                            <i class="fa-solid fa-print"></i>

                                                            <span>
                                                                Cetak
                                                            </span>

                                                        </a>


                                                        <form
                                                            action="{{ route('merchant.qr.destroy', encrypt($qr->id)) }}"
                                                            method="POST"
                                                            class="delete-qr-form">

                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl text-xs font-extrabold transition border border-rose-200"
                                                                title="Hapus QR Code">

                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </form>

                                                    </div>

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="5" class="p-12 text-center">

                                                    <div
                                                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">

                                                        <i class="fa-solid fa-qrcode"></i>

                                                    </div>

                                                    <p class="font-bold text-slate-700 text-sm">
                                                        Belum ada QR Code yang dibuat
                                                    </p>

                                                    <p class="text-xs text-slate-400 mt-1">
                                                        Gunakan form di samping untuk membuat QR Code meja baru.
                                                    </p>

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </main>

        </div>

    </div>

@endsection
