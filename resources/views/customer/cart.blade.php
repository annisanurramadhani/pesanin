@extends('layouts.customer')

@section('disableSweetAlert')
@endsection

@section('content')

    <div class="min-h-screen">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 py-5">

                <div class="flex items-center justify-between gap-4">

                    <div class="flex items-center gap-3 min-w-0">

                        <a href="{{ route('customer.menu', $qrCode->code) }}"
                            class="w-10 h-10 shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                            <i class="fa-solid fa-arrow-left text-sm"></i>
                        </a>

                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                Keranjang
                            </h1>

                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 truncate">
                                {{ $merchant->name ?? 'PesanIn' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>
        </div>


        {{-- Main --}}
        <main class="max-w-3xl mx-auto px-4 sm:px-6 py-6 pb-10">

            {{-- Flash Message --}}
            @if (session('success'))
                <div id="cartSuccessNotification"
                    class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 opacity-100 translate-y-0 transition-all duration-300">

                    <div
                        class="w-8 h-8 shrink-0 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-check text-sm"></i>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-emerald-800">
                            Berhasil
                        </p>

                        <p class="text-xs text-emerald-700 mt-0.5">
                            {{ session('success') }}
                        </p>
                    </div>

                </div>
            @endif


            @if (session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5">

                    <div class="w-8 h-8 shrink-0 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-red-800">
                            Terjadi Kesalahan
                        </p>

                        <p class="text-xs text-red-700 mt-0.5">
                            {{ session('error') }}
                        </p>
                    </div>

                </div>
            @endif


            {{-- Cart Empty --}}
            @if (empty($cartItems))

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm px-6 py-12 sm:px-10 text-center">

                    <div
                        class="mx-auto w-20 h-20 rounded-3xl bg-amber-50 text-amber-500 flex items-center justify-center mb-5">
                        <i class="fa-solid fa-cart-shopping text-3xl"></i>
                    </div>

                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        Keranjang masih kosong
                    </h2>

                    <p class="text-sm text-slate-500 mt-2 max-w-sm mx-auto">
                        Belum ada menu yang kamu pilih. Yuk, pilih makanan dan minuman favoritmu.
                    </p>

                    <a href="{{ route('customer.menu', $qrCode->code) }}"
                        class="inline-flex items-center gap-2 mt-6 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold transition shadow-sm">

                        <i class="fa-solid fa-utensils text-xs"></i>

                        Lihat Menu

                    </a>

                </div>
            @else
                {{-- Cart Information --}}
                <div class="flex items-center justify-between mb-4">

                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                            Pesanan Kamu
                        </p>

                        <h2 class="text-lg font-extrabold text-slate-900 mt-1">
                            {{ count($cartItems) }} Menu
                        </h2>
                    </div>

                    <a href="{{ route('customer.menu', $qrCode->code) }}?keep_voucher=1"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-amber-300 bg-amber-50 text-amber-600 hover:bg-amber-100 transition">

                        <i class="fa-solid fa-plus text-xs"></i>

                        <span class="text-xs sm:text-sm font-extrabold">
                            Tambah Menu
                        </span>

                    </a>

                </div>


                {{-- Cart Items --}}
                <div class="space-y-3">

                    @foreach ($cartItems as $item)
                        @php
                            $menu = $item['menu'];
                            $quantity = $item['quantity'];
                            $subtotal = $item['subtotal'];
                        @endphp

                        <div data-cart-item="{{ $menu->id }}"
                            class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden">

                            <div class="p-4">

                                <div class="flex gap-3 sm:gap-4">

                                    {{-- Image --}}
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 shrink-0">

                                        <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('assets/images/menu-default.jpg') }}"
                                            alt="{{ $menu->name }}" class="w-full h-full object-cover rounded-xl">

                                    </div>


                                    {{-- Detail --}}
                                    <div class="flex-1 min-w-0">

                                        <div class="flex items-start justify-between gap-3">

                                            <div class="min-w-0">

                                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 truncate">
                                                    {{ $menu->name }}
                                                </h3>

                                                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                                </p>

                                            </div>


                                            {{-- Remove --}}
                                            <form
                                                action="{{ route('customer.cart.remove', [
                                                    'code' => $qrCode->code,
                                                    'menuId' => $menu->id,
                                                ]) }}"
                                                method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Hapus menu"
                                                    class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition">

                                                    <i class="fa-solid fa-trash-can text-xs"></i>

                                                </button>

                                            </form>

                                        </div>


                                        {{-- Bottom --}}
                                        <div class="flex items-end justify-between gap-3 mt-3">

                                            {{-- Quantity --}}
                                            <form action="{{ route('customer.cart.update', $qrCode->code) }}"
                                                method="POST"
                                                class="quantity-form inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1">

                                                @csrf
                                                @method('PATCH')

                                                <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                                                <input type="hidden" name="quantity" value="{{ $quantity }}">

                                                <button type="button" data-action="minus"
                                                    class="w-8 h-8 rounded-lg text-slate-600 hover:bg-white hover:shadow-sm flex items-center justify-center transition">

                                                    <i class="fa-solid fa-minus text-[10px]"></i>

                                                </button>

                                                <span data-quantity
                                                    class="w-8 text-center text-xs sm:text-sm font-extrabold text-slate-800">
                                                    {{ $quantity }}
                                                </span>

                                                <button type="button" data-action="plus"
                                                    class="w-8 h-8 rounded-lg text-slate-600 hover:bg-white hover:shadow-sm flex items-center justify-center transition">

                                                    <i class="fa-solid fa-plus text-[10px]"></i>

                                                </button>

                                            </form>


                                            {{-- Subtotal --}}
                                            <div class="text-right">

                                                <p class="text-[10px] sm:text-xs text-slate-400 font-medium">
                                                    Subtotal
                                                </p>

                                                <p data-subtotal
                                                    class="text-sm sm:text-base font-extrabold text-slate-900 mt-0.5">
                                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>


                {{-- Voucher --}}
                <div class="mt-6">
                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="p-5 sm:p-6" id="voucherSectionContainer">

                            <div class="flex items-center gap-2 mb-4">

                                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <i class="fa-solid fa-ticket text-xs"></i>
                                </div>

                                <div>
                                    <h2 class="text-sm font-extrabold text-slate-900">
                                        Kode Voucher
                                    </h2>

                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Punya kode voucher? Masukkan di sini.
                                    </p>
                                </div>

                            </div>


                            @if (is_array($cartVoucher ?? null) && !empty($cartVoucher['code']))
                                {{-- Voucher Aktif --}}
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">

                                    <div class="flex items-center justify-between gap-3">

                                        <div class="min-w-0">

                                            <div class="flex items-center gap-2">

                                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>

                                                <p class="text-sm font-extrabold text-emerald-800">
                                                    {{ $cartVoucher['code'] }}
                                                </p>

                                            </div>

                                            <p class="text-xs text-emerald-700 mt-1">
                                                Voucher berhasil diterapkan.
                                            </p>

                                        </div>

                                        <div class="flex items-center gap-3 shrink-0">

                                            <div class="text-right">

                                                <p class="text-[10px] text-emerald-600 font-medium">
                                                    Diskon
                                                </p>

                                                <p class="text-sm font-extrabold text-emerald-700">
                                                    -Rp {{ number_format($discount, 0, ',', '.') }}
                                                </p>

                                            </div>

                                            {{-- Tombol Hapus Voucher --}}
                                            <form id="removeVoucherForm"
                                                action="{{ route('customer.cart.voucher.remove', $qrCode->code) }}"
                                                method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Hapus voucher" aria-label="Hapus voucher"
                                                    class="w-8 h-8 rounded-lg bg-white border border-emerald-200 text-emerald-600 hover:bg-red-50 hover:border-red-200 hover:text-red-500 flex items-center justify-center transition">

                                                    <i class="fa-solid fa-xmark text-sm"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                </div>
                            @else
                                {{-- Input Voucher --}}
                                <form id="voucherForm" action="{{ route('customer.cart.voucher', $qrCode->code) }}"
                                    method="POST" class="flex gap-2">

                                    @csrf

                                    <div class="relative flex-1">

                                        <i
                                            class="fa-solid fa-ticket absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">
                                        </i>

                                        <input type="text" name="voucher_code" id="voucherCode" maxlength="50"
                                            autocomplete="off" placeholder="Masukkan kode voucher"
                                            class="w-full h-11 pl-9 pr-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-200 focus:border-amber-400 transition uppercase">

                                    </div>

                                    <button type="submit" id="voucherButton"
                                        class="h-11 px-5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-extrabold transition shadow-sm">

                                        <span id="voucherButtonText">
                                            OK
                                        </span>

                                    </button>

                                </form>
                            @endif

                        </div>

                    </div>
                </div>

                {{-- Summary --}}
                <div class="mt-6">

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="p-5 sm:p-6">

                            <div class="flex items-center gap-2 mb-5">

                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                </div>

                                <h2 class="text-sm font-extrabold text-slate-900">
                                    Ringkasan Pesanan
                                </h2>

                            </div>


                            <div class="space-y-3">

                                {{-- Jumlah Item --}}
                                <div class="flex items-center justify-between text-sm">

                                    <span class="text-slate-500">
                                        Total menu
                                    </span>

                                    <span class="font-semibold text-slate-700">
                                        {{ count($cartItems) }} item
                                    </span>

                                </div>


                                {{-- Subtotal --}}
                                <div class="flex items-center justify-between text-sm">

                                    <span class="text-slate-500">
                                        Subtotal
                                    </span>

                                    <span id="cart-subtotal" class="font-semibold text-slate-700">
                                        Rp {{ number_format($total + $discount, 0, ',', '.') }}
                                    </span>

                                </div>


                                {{-- Voucher --}}
                                <div id="discountRowContainer">
                                    @if ($discount > 0)
                                        <div class="flex items-center justify-between text-sm">

                                            <span class="text-emerald-600">
                                                Voucher
                                                @if (is_array($cartVoucher ?? null) && !empty($cartVoucher['code']))
                                                    <span class="font-bold" id="activeVoucherCodeText">
                                                        ({{ $cartVoucher['code'] }})
                                                    </span>
                                                @endif
                                            </span>

                                            <span id="cart-discount" class="font-bold text-emerald-600">
                                                -Rp {{ number_format($discount, 0, ',', '.') }}
                                            </span>

                                        </div>
                                    @endif
                                </div>


                                <div class="border-t border-dashed border-slate-200"></div>


                                {{-- Total --}}
                                <div class="flex items-center justify-between">

                                    <span class="text-sm font-bold text-slate-700">
                                        Total Pesanan
                                    </span>

                                    <span id="cart-total" class="text-xl sm:text-2xl font-extrabold text-slate-900">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>

                                </div>

                            </div>


                            {{-- Checkout --}}
                            <a href="{{ route('customer.checkout', $qrCode->code) }}"
                                class="flex items-center justify-center gap-2 w-full mt-6 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-extrabold transition shadow-sm">

                                Lanjutkan Pesanan

                                <i class="fa-solid fa-arrow-right text-xs"></i>

                            </a>


                            <p class="text-center text-[11px] text-slate-400 mt-3">
                                Pastikan pesanan kamu sudah sesuai sebelum melanjutkan.
                            </p>

                        </div>

                    </div>

                </div>

            @endif

        </main>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qrCodeCode = "{{ $qrCode->code }}";

            // 1. FORM VOUCHER AJAX
            const voucherForm = document.getElementById('voucherForm');
            if (voucherForm) {
                voucherForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const voucherInput = document.getElementById('voucherCode');
                    const voucherButton = document.getElementById('voucherButton');
                    const voucherButtonText = document.getElementById('voucherButtonText');
                    const voucherCode = voucherInput ? voucherInput.value.trim() : '';

                    if (!voucherCode) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kode Voucher Kosong',
                            text: 'Silakan masukkan kode voucher terlebih dahulu.',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#f59e0b',
                            background: '#ffffff',
                            color: '#111827',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                            }
                        });
                        return;
                    }

                    if (voucherButton) voucherButton.disabled = true;
                    if (voucherButtonText) voucherButtonText.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin"></i>';

                    const formData = new FormData(voucherForm);

                    try {
                        const response = await fetch(voucherForm.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (!data.success) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Voucher Tidak Bisa Digunakan',
                                text: data.message,
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#f59e0b',
                                background: '#ffffff',
                                color: '#111827',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                                }
                            });
                            return;
                        }

                        // Sukses: Langsung reload halaman secara mulus untuk memperbarui state session voucher
                        window.location.reload();

                    } catch (error) {
                        console.error(error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menerapkan voucher. Silakan coba lagi.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#111827',
                            background: '#ffffff',
                            color: '#111827',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                            }
                        });
                    } finally {
                        if (voucherButton) voucherButton.disabled = false;
                        if (voucherButtonText) voucherButtonText.textContent = 'OK';
                    }
                });
            }

            // 2. FORM HAPUS VOUCHER AJAX
            const removeVoucherForm = document.getElementById('removeVoucherForm');
            if (removeVoucherForm) {
                removeVoucherForm.addEventListener('submit', async function(event) {
                    event.preventDefault();
                    const formData = new FormData(removeVoucherForm);
                    formData.append('_method', 'DELETE');

                    try {
                        const response = await fetch(removeVoucherForm.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error(error);
                    }
                });
            }

            // 3. SCRIPT KUANTITAS (+ / -)
            document.querySelectorAll('.quantity-form').forEach(function(form) {
                const minusButton = form.querySelector('[data-action="minus"]');
                const plusButton = form.querySelector('[data-action="plus"]');
                const quantityInput = form.querySelector('[name="quantity"]');
                const quantityText = form.querySelector('[data-quantity]');
                const cartItem = form.closest('[data-cart-item]');
                const subtotalElement = cartItem.querySelector('[data-subtotal]');

                async function updateQuantity(quantity) {
                    if (quantity < 0) return;

                    minusButton.disabled = true;
                    plusButton.disabled = true;

                    const formData = new FormData(form);
                    formData.set('quantity', quantity);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (!data.success) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Stok Tidak Mencukupi',
                                text: data.message,
                                confirmButtonText: 'Mengerti',
                                confirmButtonColor: '#f59e0b',
                                background: '#ffffff',
                                color: '#111827',
                                customClass: {
                                    popup: 'rounded-2xl',
                                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                                }
                            });
                            return;
                        }

                        if (data.removed) {
                            cartItem.remove();
                            window.location.reload();
                            return;
                        }

                        quantityInput.value = data.quantity;
                        quantityText.textContent = data.quantity;
                        subtotalElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data
                            .subtotal);

                        document.querySelector('#cart-total').textContent = 'Rp ' + new Intl
                            .NumberFormat('id-ID').format(data.total);

                        const cartSubtotal = document.querySelector('#cart-subtotal');
                        if (cartSubtotal) {
                            cartSubtotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                                data.cart_subtotal);
                        }

                        const cartDiscount = document.querySelector('#cart-discount');
                        if (cartDiscount) {
                            cartDiscount.textContent = data.discount > 0 ? ('-Rp ' + new Intl
                                .NumberFormat('id-ID').format(data.discount)) : '-Rp 0';
                        }

                        if (data.voucher_removed) {
                            window.location.reload();
                        }

                    } catch (error) {
                        console.error(error);
                    } finally {
                        minusButton.disabled = false;
                        plusButton.disabled = false;
                    }
                }

                minusButton.addEventListener('click', function() {
                    const quantity = parseInt(quantityInput.value);
                    updateQuantity(quantity - 1);
                });

                plusButton.addEventListener('click', function() {
                    const quantity = parseInt(quantityInput.value);
                    updateQuantity(quantity + 1);
                });
            });
        });
    </script>

    <script>
        function bindVoucherFormEvent() {
            const voucherForm = document.getElementById('voucherForm');
            if (!voucherForm) return;

            const voucherInput = document.getElementById('voucherCode');
            const voucherButton = document.getElementById('voucherButton');
            const voucherButtonText = document.getElementById('voucherButtonText');

            voucherForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                const voucherCode = voucherInput.value.trim();

                if (!voucherCode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kode Voucher Kosong',
                        text: 'Silakan masukkan kode voucher terlebih dahulu.',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#f59e0b',
                        background: '#ffffff',
                        color: '#111827',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                        }
                    });
                    return;
                }

                voucherButton.disabled = true;
                voucherButtonText.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                const formData = new FormData(voucherForm);

                try {
                    const response = await fetch(
                        voucherForm.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        }
                    );

                    const data = await response.json();

                    if (!data.success) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Voucher Tidak Bisa Digunakan',
                            text: data.message,
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#f59e0b',
                            background: '#ffffff',
                            color: '#111827',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                            }
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Voucher Berhasil!',
                        text: data.voucher_code + ' berhasil digunakan.',
                        confirmButtonText: 'Lanjut',
                        confirmButtonColor: '#111827',
                        background: '#ffffff',
                        color: '#111827',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                        }
                    }).then(function() {
                        const cartTotal = document.querySelector('#cart-total');
                        if (cartTotal) {
                            cartTotal.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data
                                .total);
                        }

                        let cartDiscount = document.querySelector('#cart-discount');
                        if (cartDiscount) {
                            cartDiscount.textContent = '-Rp ' + new Intl.NumberFormat('id-ID').format(
                                data.discount);
                        } else if (data.discount > 0) {
                            const discountHtml = `
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-emerald-600">
                                Voucher <span class="font-bold" id="activeVoucherCodeText">(${data.voucher_code})</span>
                            </span>
                            <span id="cart-discount" class="font-bold text-emerald-600">
                                -Rp ${new Intl.NumberFormat('id-ID').format(data.discount)}
                            </span>
                        </div>
                    `;
                            const subtotalDiv = document.querySelector('#cart-subtotal').closest(
                                '.flex.items-center.justify-between.text-sm');
                            subtotalDiv.insertAdjacentHTML('afterend', discountHtml);
                        }

                        const voucherSection = document.getElementById('voucherSectionContainer');
                        if (voucherSection) {
                            const activeVoucherHtml = `
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i class="fa-solid fa-ticket text-xs"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Kode Voucher</h2>
                                <p class="text-[11px] text-slate-400 mt-0.5">Punya kode voucher? Masukkan di sini.</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                        <p class="text-sm font-extrabold text-emerald-800">${data.voucher_code}</p>
                                    </div>
                                    <p class="text-xs text-emerald-700 mt-1">Voucher berhasil diterapkan.</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <div class="text-right">
                                        <p class="text-[10px] text-emerald-600 font-medium">Diskon</p>
                                        <p class="text-sm font-extrabold text-emerald-700">-Rp ${new Intl.NumberFormat('id-ID').format(data.discount)}</p>
                                    </div>
                                    <form id="removeVoucherForm" action="{{ route('customer.cart.voucher.remove', $qrCode->code) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus voucher" aria-label="Hapus voucher" class="w-8 h-8 rounded-lg bg-white border border-emerald-200 text-emerald-600 hover:bg-red-50 hover:border-red-200 hover:text-red-500 flex items-center justify-center transition">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                            voucherSection.innerHTML = activeVoucherHtml;

                            // Pasang kembali event listener pada form hapus voucher yang baru saja dirender
                            const newRemoveForm = document.getElementById('removeVoucherForm');
                            if (newRemoveForm) {
                                // Memicu ulang event listener hapus voucher
                                location
                                    .reload(); // Opsional agar langsung sinkron sempurna tanpa kendala scope DOM dinamis
                            }
                        }
                    });

                } catch (error) {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal menerapkan voucher. Silakan coba lagi.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#111827',
                        background: '#ffffff',
                        color: '#111827',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-5 py-2.5 font-bold'
                        }
                    });
                } finally {
                    voucherButton.disabled = false;
                    voucherButtonText.textContent = 'OK';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            bindVoucherFormEvent();
        });
    </script>

    {{-- =========================================================
    AUTO HIDE SUCCESS NOTIFICATION
========================================================= --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const notification = document.getElementById(
                    'cartSuccessNotification'
                );

                if (!notification) {
                    return;
                }

                setTimeout(function() {

                    notification.classList.remove(
                        'opacity-100',
                        'translate-y-0'
                    );

                    notification.classList.add(
                        'opacity-0',
                        'translate-y-2'
                    );

                    setTimeout(function() {
                        notification.remove();
                    }, 300);

                }, 1000);

            });
        </script>
    @endif

@endsection
