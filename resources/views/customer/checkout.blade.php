@extends('layouts.customer')

@section('content')

<div class="min-h-screen bg-[#F3F4F8]">

    {{-- Header --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-5">

            <div class="flex items-center gap-3">

                <a
                    href="{{ route('customer.cart', $qrCode->code) }}"
                    class="w-10 h-10 rounded-xl border border-slate-200
                           flex items-center justify-center
                           text-slate-500 hover:bg-slate-50 transition"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">
                        Checkout
                    </h1>

                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $merchant->name ?? 'PesanIn' }}
                    </p>
                </div>

            </div>

        </div>
    </div>


    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6">

        {{-- Flash Message Success --}}
        @if (session('success'))

            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3">

                <div class="flex items-start gap-3">

                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600
                                flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-check"></i>

                    </div>

                    <p class="text-sm text-emerald-700 pt-1.5">
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        @endif

        {{-- Flash Message Error --}}
        @if (session('error'))

            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">

                <div class="flex items-start gap-3">

                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600
                                flex items-center justify-center flex-shrink-0">

                        <i class="fa-solid fa-circle-exclamation"></i>

                    </div>

                    <p class="text-sm text-red-700 pt-1.5">
                        {{ session('error') }}
                    </p>

                </div>

            </div>

        @endif

        {{-- Validation Errors List Notification --}}
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-800">Mohon periksa kembali inputan Anda:</p>
                        <ul class="mt-1 text-xs text-red-700 list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif


        <form
            action="{{ route('customer.checkout.store', $qrCode->code) }}"
            method="POST"
        >

            @csrf


            {{-- ========================================= --}}
            {{-- DATA PELANGGAN --}}
            {{-- ========================================= --}}

            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">

                <div class="flex items-center gap-3 mb-6">

                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-user"></i>

                    </div>

                    <div>

                        <h2 class="font-bold text-slate-900">
                            Data Pelanggan
                        </h2>

                        <p class="text-xs text-slate-500 mt-0.5">
                            Masukkan informasi untuk pesanan Anda
                        </p>

                    </div>

                </div>


                {{-- Nama --}}
                <div class="mb-5">

                    <label
                        for="customer_name"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Nama
                        <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-4
                                    flex items-center pointer-events-none">

                            <i class="fa-regular fa-user text-slate-400"></i>

                        </div>

                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            value="{{ old('customer_name') }}"
                            required
                            maxlength="50"
                            pattern="[a-zA-Z\s]+"
                            title="Nama hanya boleh diisi huruf dan spasi"
                            placeholder="Masukkan nama Anda"
                            class="w-full rounded-xl border @error('customer_name') border-red-500 bg-red-50/30 @else border-slate-200 bg-slate-50 @enderror
                                   pl-11 pr-4 py-3
                                   text-sm text-slate-800
                                   outline-none transition
                                   placeholder:text-slate-400
                                   focus:border-amber-400
                                   focus:bg-white
                                   focus:ring-2 focus:ring-amber-100"
                        >

                    </div>

                    @error('customer_name')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Nomor Telepon --}}
                <div class="mb-5">

                    <label
                        for="customer_phone"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Nomor Telepon

                        <span class="ml-1 text-xs font-normal text-slate-400">
                            (opsional)
                        </span>
                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-4
                                    flex items-center pointer-events-none">

                            <i class="fa-solid fa-phone text-slate-400"></i>

                        </div>

                        <input
                            type="tel"
                            id="customer_phone"
                            name="customer_phone"
                            value="{{ old('customer_phone') }}"
                            maxlength="14"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-xl border @error('customer_phone') border-red-500 bg-red-50/30 @else border-slate-200 bg-slate-50 @enderror
                                   pl-11 pr-4 py-3
                                   text-sm text-slate-800
                                   outline-none transition
                                   placeholder:text-slate-400
                                   focus:border-amber-400
                                   focus:bg-white
                                   focus:ring-2 focus:ring-amber-100"
                        >

                    </div>

                    @error('customer_phone')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Email --}}
                <div>

                    <label
                        for="customer_email"
                        class="block text-sm font-semibold text-slate-700 mb-2"
                    >
                        Email

                        <span class="ml-1 text-xs font-normal text-slate-400">
                            (opsional)
                        </span>
                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-4
                                    flex items-center pointer-events-none">

                            <i class="fa-regular fa-envelope text-slate-400"></i>

                        </div>

                        <input
                            type="email"
                            id="customer_email"
                            name="customer_email"
                            value="{{ old('customer_email') }}"
                            maxlength="100"
                            placeholder="Contoh: nama@email.com"
                            class="w-full rounded-xl border @error('customer_email') border-red-500 bg-red-50/30 @else border-slate-200 bg-slate-50 @enderror
                                   pl-11 pr-4 py-3
                                   text-sm text-slate-800
                                   outline-none transition
                                   placeholder:text-slate-400
                                   focus:border-amber-400
                                   focus:bg-white
                                   focus:ring-2 focus:ring-amber-100"
                        >

                    </div>

                    <div class="flex items-start gap-2 mt-2">

                        <i class="fa-solid fa-circle-info text-[11px]
                                  text-slate-400 mt-0.5"></i>

                        <p class="text-xs text-slate-400 leading-relaxed">
                            Email dapat digunakan untuk mengirimkan struk
                            apabila menggunakan pembayaran QRIS.
                        </p>

                    </div>

                    @error('customer_email')
                        <p class="mt-1.5 text-xs text-red-500 font-medium">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- ========================================= --}}
            {{-- METODE PEMBAYARAN --}}
            {{-- ========================================= --}}

            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 mt-5">

                <div class="flex items-center gap-3 mb-6">

                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-wallet"></i>

                    </div>

                    <div>

                        <h2 class="font-bold text-slate-900">
                            Metode Pembayaran
                        </h2>

                        <p class="text-xs text-slate-500 mt-0.5">
                            Pilih metode pembayaran
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">


                    {{-- Cash --}}
                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="payment_method"
                            value="cash"
                            class="peer sr-only"
                            {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}
                        >

                        <div
                            class="rounded-2xl border border-slate-200 p-4
                                   transition
                                   peer-checked:border-amber-400
                                   peer-checked:bg-amber-50
                                   hover:border-slate-300"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl bg-emerald-50
                                           text-emerald-600
                                           flex items-center justify-center"
                                >
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>

                                <div>

                                    <p class="text-sm font-bold text-slate-800">
                                        Tunai
                                    </p>

                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Bayar langsung di kasir
                                    </p>

                                </div>

                            </div>

                        </div>

                    </label>


                    {{-- QRIS --}}
                    <label class="cursor-pointer">

                        <input
                            type="radio"
                            name="payment_method"
                            value="qris"
                            class="peer sr-only"
                            {{ old('payment_method') === 'qris' ? 'checked' : '' }}
                        >

                        <div
                            class="rounded-2xl border border-slate-200 p-4
                                   transition
                                   peer-checked:border-amber-400
                                   peer-checked:bg-amber-50
                                   hover:border-slate-300"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl bg-slate-100
                                           text-slate-700
                                           flex items-center justify-center"
                                >
                                    <i class="fa-solid fa-qrcode"></i>
                                </div>

                                <div>

                                    <p class="text-sm font-bold text-slate-800">
                                        QRIS
                                    </p>

                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Bayar dengan QRIS
                                    </p>

                                </div>

                            </div>

                        </div>

                    </label>

                </div>


                @error('payment_method')
                    <p class="mt-2 text-xs text-red-500 font-medium">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                    </p>
                @enderror

            </div>


            {{-- ========================================= --}}
            {{-- RINGKASAN PESANAN --}}
            {{-- ========================================= --}}

            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 mt-5">

                <div class="flex items-center gap-3 mb-5">

                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600
                                flex items-center justify-center">

                        <i class="fa-solid fa-receipt"></i>

                    </div>

                    <div>

                        <h2 class="font-bold text-slate-900">
                            Ringkasan Pesanan
                        </h2>

                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ count($cartItems) }} item dalam pesanan
                        </p>

                    </div>

                </div>


                <div class="space-y-3">

                    @foreach ($cartItems as $item)

                        @php
                            $menu = $item['menu'];
                            $quantity = $item['quantity'];
                            $subtotal = $item['subtotal'];
                        @endphp

                        <div class="flex items-center justify-between gap-4">

                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-slate-700 truncate">
                                    {{ $menu->name }}
                                </p>

                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $quantity }} ×
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </p>

                            </div>

                            <p class="text-sm font-semibold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </p>

                        </div>

                    @endforeach

                </div>


                <div class="border-t border-slate-100 mt-5 pt-5">

                    <div class="flex items-center justify-between">

                        <span class="text-sm font-medium text-slate-500">
                            Total Pembayaran
                        </span>

                        <span class="text-xl font-extrabold text-slate-900">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- ========================================= --}}
            {{-- SUBMIT --}}
            {{-- ========================================= --}}

            <button
                type="submit"
                class="w-full mt-5 rounded-2xl bg-slate-900
                       px-5 py-4
                       text-sm font-bold text-white
                       shadow-lg shadow-slate-900/10
                       hover:bg-slate-800
                       active:scale-[0.99]
                       transition cursor-pointer"
            >

                <i class="fa-solid fa-check mr-2"></i>

                Buat Pesanan

            </button>


            <p class="text-center text-xs text-slate-400 mt-3">
                Dengan melanjutkan, pastikan data pesanan Anda sudah benar.
            </p>

        </form>

    </div>

</div>

@endsection