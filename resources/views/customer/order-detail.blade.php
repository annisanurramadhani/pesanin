@extends('layouts.customer')

@section('content')

    <div
    id="order-detail-container"
    data-status-url="{{ route(
        'customer.order.detail.status',
        [
            'code' => $qrCode->code,
            'orderNumber' => request()->route('orderNumber'),
        ]
    ) }}">

    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- =========================================================
            HEADER
        ========================================================== --}}
        <div class="text-center mb-6">

            <div
                class="mx-auto flex h-16 w-16 items-center justify-center
                       rounded-full bg-emerald-50">

                <i
                    class="fa-solid fa-receipt
                           text-3xl text-emerald-500">
                </i>

            </div>

            <h1
                class="mt-4 text-2xl font-black
                       text-slate-900">

                Detail Pesanan

            </h1>

            <p
                class="mt-1 text-sm text-slate-500">

                Rincian pesanan dan pembayaran kamu.

            </p>

        </div>


        {{-- =========================================================
            ORDER INFORMATION
        ========================================================== --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-5 mb-5">

            <div
                class="flex items-center
                       justify-between gap-4">

                <div>

                    <p
                        class="text-xs font-medium
                               text-slate-400
                               uppercase tracking-wider">

                        ID Pesanan

                    </p>

                    <p
                        class="mt-1 text-sm font-bold
                               text-slate-900">

                        {{ $order->order_number }}

                    </p>

                </div>


                <div class="text-right">

                    <p
                        class="text-xs font-medium
                               text-slate-400
                               uppercase tracking-wider">

                        Status

                    </p>

                    <span
                        id="order-overall-status"
                        class="mt-1 inline-flex
                               items-center gap-1.5
                               px-3 py-1.5
                               rounded-full
                               bg-emerald-50
                               text-emerald-700
                               text-xs font-bold">

                        <span
                            class="w-1.5 h-1.5
                                   rounded-full
                                   bg-emerald-500">
                        </span>

                        Lunas

                    </span>

                </div>

            </div>

        </div>


        {{-- =========================================================
            CUSTOMER
        ========================================================== --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-5 mb-5">

            <p
                class="text-sm font-bold
                       text-slate-900 mb-4">

                Informasi Pemesan

            </p>


            <div class="space-y-3">

                <div
                    class="flex items-center
                           justify-between gap-4">

                    <span
                        class="text-sm text-slate-500">

                        Nama

                    </span>

                    <span
                        class="text-sm font-semibold
                               text-slate-800
                               text-right">

                        {{ $order->customer_name }}

                    </span>

                </div>


                @if ($order->customer_phone)

                    <div
                        class="flex items-center
                               justify-between gap-4">

                        <span
                            class="text-sm text-slate-500">

                            Telepon

                        </span>

                        <span
                            class="text-sm font-semibold
                                   text-slate-800">

                            {{ $order->customer_phone }}

                        </span>

                    </div>

                @endif


                @if ($order->customer_email)

                    <div
                        class="flex items-center
                               justify-between gap-4">

                        <span
                            class="text-sm text-slate-500">

                            Email

                        </span>

                        <span
                            class="text-sm font-semibold
                                   text-slate-800
                                   text-right
                                   break-all">

                            {{ $order->customer_email }}

                        </span>

                    </div>

                @endif

            </div>

        </div>


        {{-- =========================================================
            ORDER ITEMS
        ========================================================== --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-5 mb-5">

            <p
                class="text-sm font-bold
                       text-slate-900 mb-4">

                Detail Pesanan

            </p>


            <div class="space-y-5">

                @foreach ($order->items as $item)

                    <div
                        class="rounded-xl
                            border border-slate-100
                            bg-slate-50
                            p-4">

                        {{-- =================================================
                            MENU HEADER
                        ================================================== --}}
                        <div
                            class="flex items-start
                                justify-between gap-4">

                            <div class="min-w-0">

                                <p
                                    class="text-sm font-bold
                                        text-slate-800">

                                    {{ $item->menu_name }}

                                </p>

                                <p
                                    class="mt-1 text-xs
                                        text-slate-400">

                                    {{ $item->quantity }}
                                    ×
                                    Rp
                                    {{ number_format(
                                        $item->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </p>

                            </div>


                            <p
                                class="text-sm font-bold
                                    text-slate-800
                                    whitespace-nowrap">

                                Rp
                                {{ number_format(
                                    $item->subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </p>

                        </div>


                        {{-- =================================================
                            STATUS SETIAP UNIT
                        ================================================== --}}
                        <div
                            class="mt-4
                                space-y-2">

                            @foreach ($item->unit as $unit)

                                @if ($unit->status === 'pending' || $unit->status === 'processing')

                                    {{-- SEDANG DI MASAK --}}
                                    <div
                                        data-unit-id="{{ $unit->id }}"
                                        data-status="{{ $unit->status }}"
                                        class="order-unit-status
                                            flex items-center
                                            justify-between
                                            gap-3
                                            rounded-lg
                                            bg-amber-50
                                            border border-amber-100
                                            px-3 py-2">

                                        <div
                                            class="flex items-center
                                                gap-2">

                                            <span
                                                class="unit-status-icon
                                                class="flex h-7 w-7
                                                    items-center
                                                    justify-center
                                                    rounded-full
                                                    bg-amber-100">

                                                <i
                                                    class="fa-solid fa-fire
                                                        text-xs
                                                        text-amber-600">
                                                </i>

                                            </span>

                                            <span
                                                class="text-xs
                                                    font-semibold
                                                    text-amber-700">

                                                Menu {{ $unit->unit_number }}

                                            </span>

                                        </div>


                                        <span
                                            class="unit-status-text
                                            class="text-xs
                                                font-bold
                                                text-amber-700">

                                            Sedang di Masak

                                        </span>

                                    </div>


                                @elseif ($unit->status === 'completed')

                                    {{-- SELESAI --}}
                                    <div
                                        data-unit-id="{{ $unit->id }}"
                                        data-status="{{ $unit->status }}"
                                        class="order-unit-status
                                            flex items-center
                                            justify-between
                                            gap-3
                                            rounded-lg
                                            bg-amber-50
                                            border border-amber-100
                                            px-3 py-2">

                                        <div
                                            class="flex items-center
                                                gap-2">

                                            <span
                                                class="unit-status-icon
                                                class="flex h-7 w-7
                                                    items-center
                                                    justify-center
                                                    rounded-full
                                                    bg-emerald-100">

                                                <i
                                                    class="fa-solid fa-check
                                                        text-xs
                                                        text-emerald-600">
                                                </i>

                                            </span>

                                            <span
                                                class="text-xs
                                                    font-semibold
                                                    text-emerald-700">

                                                Menu {{ $unit->unit_number }}

                                            </span>

                                        </div>


                                        <span
                                            class="text-xs
                                                font-bold
                                                text-emerald-700">

                                            Selesai

                                        </span>

                                    </div>


                                @elseif ($unit->status === 'cancelled')

                                    {{-- BAHAN HABIS --}}
                                    <div
                                        data-unit-id="{{ $unit->id }}"
                                        data-status="{{ $unit->status }}"
                                        class="order-unit-status
                                            flex items-center
                                            justify-between
                                            gap-3
                                            rounded-lg
                                            bg-amber-50
                                            border border-amber-100
                                            px-3 py-2">

                                        <div
                                            class="flex items-center
                                                gap-2">

                                            <span
                                                class="unit-status-icon
                                                class="flex h-7 w-7
                                                    items-center
                                                    justify-center
                                                    rounded-full
                                                    bg-red-100">

                                                <i
                                                    class="fa-solid fa-box-open
                                                        text-xs
                                                        text-red-600">
                                                </i>

                                            </span>

                                            <span
                                                class="text-xs
                                                    font-semibold
                                                    text-red-700">

                                                Menu {{ $unit->unit_number }}

                                            </span>

                                        </div>


                                        <span
                                            class="text-xs
                                                font-bold
                                                text-red-700">

                                            Cancel (Bahan Habis)

                                        </span>

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- =========================================================
            PAYMENT
        ========================================================== --}}

        <div
            class="bg-white rounded-2xl
                border border-slate-200
                shadow-sm p-5 mb-5">

            <p
                class="text-sm font-bold
                    text-slate-900 mb-4">

                Informasi Pembayaran

            </p>


            {{-- =====================================================
                PAYMENT INFORMATION
            ====================================================== --}}

            <div class="space-y-3">

                {{-- METODE PEMBAYARAN --}}
                <div
                    class="flex items-center
                        justify-between gap-4">

                    <span
                        class="text-sm text-slate-500">

                        Metode Pembayaran

                    </span>

                    <span
                        class="text-sm font-bold
                            text-slate-800">

                        @if ($order->payment_method === 'qris')

                            QRIS

                        @elseif ($order->payment_method === 'bank')

                            BANK
                            @if ($order->bank)
                                ({{ strtoupper($order->bank) }})
                            @endif

                        @elseif ($order->payment_method === 'cash')

                            Tunai

                        @else

                            {{ ucfirst($order->payment_method) }}

                        @endif

                    </span>

                </div>


                {{-- STATUS PEMBAYARAN --}}
                <div
                    class="flex items-center
                        justify-between gap-4">

                    <span
                        class="text-sm text-slate-500">

                        Status Pembayaran

                    </span>

                    <span
                        class="inline-flex items-center
                            gap-1.5 px-3 py-1.5
                            rounded-full
                            bg-emerald-50
                            text-emerald-700
                            text-xs font-bold">

                        <span
                            class="w-1.5 h-1.5
                                rounded-full
                                bg-emerald-500">
                        </span>

                        Lunas

                    </span>

                </div>


                {{-- =================================================
                    RINGKASAN PEMBAYARAN
                ================================================== --}}

                <div
                    class="border-t border-slate-100
                        mt-4 pt-4 space-y-3">

                    {{-- SUBTOTAL --}}
                    <div
                        class="flex items-center
                            justify-between gap-4">

                        <span
                            class="text-sm text-slate-500">

                            Subtotal

                        </span>

                        <span
                            class="text-sm font-semibold
                                text-slate-700">

                            Rp
                            {{ number_format(
                                $order->subtotal,
                                0,
                                ',',
                                '.'
                            ) }}

                        </span>

                    </div>


                    {{-- DISKON --}}
                    @if ($order->discount > 0)

                        <div
                            class="flex items-center
                                justify-between gap-4">

                            <span
                                class="text-sm text-emerald-600">

                                Diskon
                                @if ($order->voucher_code)
                                    ({{ $order->voucher_code }})
                                @endif

                            </span>

                            <span
                                class="text-sm font-bold
                                    text-emerald-600">

                                -Rp
                                {{ number_format(
                                    $order->discount,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </span>

                        </div>

                    @endif


                    {{-- TOTAL --}}
                    <div
                        class="border-t border-slate-100
                            pt-4 flex items-center
                            justify-between gap-4">

                        <span
                            class="text-sm font-bold
                                text-slate-700">

                            Total Pembayaran

                        </span>

                        <span
                            class="text-xl font-black
                                text-slate-900">

                            Rp
                            {{ number_format(
                                $order->total,
                                0,
                                ',',
                                '.'
                            ) }}

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
            ACTION
        ========================================================== --}}
        <div class="space-y-3">

            <a
                href="{{ route(
                    'customer.menu',
                    $qrCode->code
                ) }}"
                class="flex items-center
                       justify-center gap-2
                       w-full py-3
                       rounded-xl
                       bg-slate-900
                       text-white
                       text-sm font-bold
                       hover:bg-slate-800
                       transition">

                <i class="fa-solid fa-utensils"></i>

                Kembali ke Menu

            </a>

        </div>


        {{-- =========================================================
            FOOTER
        ========================================================== --}}
        <div class="text-center mt-8">

            <p class="text-xs text-slate-400">

                {{ $merchant->name ?? 'PesanIn' }}

            </p>

            <p
                class="text-[11px]
                       text-slate-300 mt-1">

                Terima kasih telah memesan
                melalui PesanIn

            </p>

        </div>

    </div>

<script src="{{ asset('js/customer/order-detail.js') }}"></script>
@endsection
