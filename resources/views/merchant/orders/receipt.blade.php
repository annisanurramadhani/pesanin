@extends('layouts.merchant')

@section('header')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="flex items-center gap-2 text-2xl font-extrabold tracking-tight text-slate-900">
                <i class="fa-solid fa-receipt text-amber-500"></i>
                Struk Pesanan
            </h2>

            <p class="mt-1 text-xs font-medium text-slate-500">
                Detail transaksi #{{ $order->order_number }}
            </p>
        </div>

        <div class="no-print flex flex-wrap items-center gap-2">

            {{-- Cetak Struk --}}
            <button type="button" onclick="window.print()"
                class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm transition hover:bg-slate-800">
                <i class="fa-solid fa-print"></i>
                Cetak Struk
            </button>


            {{-- Kirim ke Email --}}
            @if ($order->customer_email)
                <form action="{{ route('merchant.orders.receipt.email', $order->id) }}" method="POST">
                    @csrf

                    <button type="submit"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-extrabold text-slate-950 shadow-sm transition hover:bg-amber-400">
                        <i class="fa-solid fa-envelope"></i>
                        Kirim ke Email
                    </button>
                </form>
            @endif


            {{-- Tutup --}}
            <button type="button" onclick="window.close()"
                class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 transition hover:bg-slate-50">
                <i class="fa-solid fa-xmark"></i>
                Tutup
            </button>

        </div>
    </div>
@endsection

@section('content')
    <div class="flex justify-center py-4">

        <div id="receipt"
            class="receipt-paper w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-5 shadow-lg">

            {{-- Merchant --}}
            <div class="text-center">

                <div
                    class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-600">
                    <i class="fa-solid fa-store text-lg"></i>
                </div>

                <h3 class="text-lg font-black text-slate-900">
                    {{ $order->merchant->name ?? 'PESANIN' }}
                </h3>

                @if ($order->merchant->address)
                    <p class="mt-1 text-[10px] leading-relaxed text-slate-500">
                        {{ $order->merchant->address }}
                    </p>
                @endif

            </div>

            <div class="my-4 border-t border-dashed border-slate-300"></div>

            {{-- Informasi Order --}}
            <div class="space-y-1.5 text-[11px]">

                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">
                        No. Order
                    </span>

                    <span class="font-black text-slate-900">
                        #{{ $order->order_number }}
                    </span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">
                        Tanggal
                    </span>

                    <span class="font-bold text-slate-700">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">
                        Area / Meja
                    </span>

                    <span class="font-bold text-slate-700">
                        {{ $order->qrCode->name ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-slate-500">
                        Pemesan
                    </span>

                    <span class="text-right font-bold text-slate-700">
                        {{ $order->customer_name }}
                    </span>
                </div>

            </div>

            <div class="my-4 border-t border-dashed border-slate-300"></div>

            {{-- Detail Item --}}
            <div class="space-y-3">

                @foreach ($order->items as $item)
                    <div>

                        <p class="text-[11px] font-extrabold text-slate-800">
                            {{ $item->menu_name ?? ($item->menu->name ?? 'Item') }}
                        </p>

                        <div class="mt-1 flex items-center justify-between gap-3 text-[10px]">

                            <span class="text-slate-500">
                                {{ $item->quantity }}
                                ×
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </span>

                            <span class="font-black text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>

                        </div>

                        @if ($item->notes)
                            <p class="mt-1 text-[9px] italic text-slate-400">
                                * {{ $item->notes }}
                            </p>
                        @endif

                    </div>
                @endforeach

            </div>

            <div class="my-4 border-t border-dashed border-slate-300"></div>

            {{-- Total --}}
            <div class="space-y-2 text-[11px]">

                <div class="flex justify-between">
                    <span class="text-slate-500">
                        Subtotal
                    </span>

                    <span class="font-bold text-slate-700">
                        Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="font-black text-slate-900">
                        TOTAL
                    </span>

                    <span class="text-lg font-black text-amber-600">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </span>
                </div>

            </div>

            <div class="my-4 border-t border-dashed border-slate-300"></div>

            {{-- Payment --}}
            <div class="text-center">

                @if (strtolower($order->payment_method) === 'qris')
                    <span
                        class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-[10px] font-black text-amber-700">
                        <i class="fa-solid fa-qrcode text-amber-600"></i>
                        QRIS
                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-100 px-3 py-1.5 text-[10px] font-black text-slate-700">
                        <i class="fa-solid fa-cash-register text-slate-500"></i>
                        Bayar Kasir
                    </span>
                @endif

            </div>

            {{-- Footer --}}
            <div class="mt-5 text-center">

                <p class="text-[11px] font-extrabold text-slate-800">
                    Terima Kasih!
                </p>

                <p class="mt-1 text-[9px] text-slate-400">
                    Pesanan diproses melalui PesanIn
                </p>

            </div>

        </div>

    </div>
@endsection

@push('styles')
    <style>
        @media print {

            @page {
                size: 58mm auto;
                margin: 0;
            }

            html,
            body {
                width: 58mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }

            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            #receipt {
                width: 58mm !important;
                max-width: 58mm !important;
                margin: 0 !important;
                padding: 8px !important;

                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush
