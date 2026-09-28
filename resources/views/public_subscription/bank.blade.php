@extends('layouts.app')

@section('title', 'Pembayaran Subscription')

@section('body')


<link rel="stylesheet"
href="{{ asset('css/subscription/payment.css') }}">



<div class="max-w-lg mx-auto px-4 py-6">


    {{-- HEADER --}}
    <div class="text-center mb-6">


        @if ($subscription->payment_status === 'pending')

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-50">

                <i class="fa-solid fa-clock text-3xl text-amber-500"></i>

            </div>


            <h1 class="mt-4 text-2xl font-black text-slate-900">
                Menunggu Pembayaran
            </h1>


            <p class="mt-1 text-sm text-slate-500">
                Silakan selesaikan pembayaran untuk mengaktifkan subscription.
            </p>


        @elseif($subscription->payment_status === 'paid')


            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50">

                <i class="fa-solid fa-circle-check text-3xl text-emerald-500"></i>

            </div>


            <h1 class="mt-4 text-2xl font-black text-slate-900">
                Pembayaran Berhasil
            </h1>



        @elseif($subscription->payment_status === 'expired')


            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-50">

                <i class="fa-solid fa-clock text-3xl text-red-500"></i>

            </div>


            <h1 class="mt-4 text-2xl font-black text-slate-900">
                Pembayaran Kedaluwarsa
            </h1>


        @endif


    </div>





    {{-- DETAIL --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">


        <div class="flex justify-between">


            <div>

                <p class="text-xs text-slate-400 uppercase">
                    ID Subscription
                </p>


                <p class="font-bold">

                    {{ $subscription->invoice_number }}

                </p>


            </div>



            <div class="text-right">

                <p class="text-xs text-slate-400 uppercase">
                    Total
                </p>


                <p class="font-black">

                    Rp {{ number_format(
                        $subscription->price,
                        0,
                        ',',
                        '.'
                    ) }}

                </p>

            </div>


        </div>


    </div>






    {{-- STATUS --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">


        <div class="flex justify-between">


            <div>

                <p class="text-xs text-slate-400 uppercase">
                    Status Pembayaran
                </p>


                <p class="font-bold">
                    BANK
                </p>

            </div>


            <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold">

                ● Menunggu Pembayaran

            </span>


        </div>




        @if($subscription->expired_at)


        <div class="mt-5 rounded-2xl bg-amber-50 border border-amber-100 p-4">


            <div class="text-center">

                <p class="font-bold text-amber-800">

                    <i class="fa-solid fa-clock"></i>

                    Selesaikan Pembayaran

                </p>



                <p
                    id="countdown"
                    data-expired="{{ $subscription->expired_at->format('Y-m-d H:i:s') }}"
                    class="mt-2 text-3xl font-black text-amber-600">

                    00:00:00

                </p>



                <p class="text-xs text-amber-600">
                    Waktu pembayaran tersisa.
                </p>


            </div>


        </div>


        @endif


    </div>






    {{-- VA --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-5">


        <div class="text-center">


            <p class="text-xs text-slate-400 uppercase">
                Total Pembayaran
            </p>


            <h2 class="text-3xl font-black">

                Rp {{ number_format(
                    $subscription->price,
                    0,
                    ',',
                    '.'
                ) }}

            </h2>



            <div class="mt-6 rounded-2xl bg-indigo-50 border border-indigo-100 p-5">


                <i class="fa-solid fa-building-columns text-3xl text-indigo-600"></i>


                <p class="mt-3 font-bold text-indigo-700">
                    Virtual Account
                </p>



                <div class="mt-5 bg-white rounded-xl p-4">


                    <p class="text-xs text-slate-400">
                        Bank
                    </p>


                    <p class="font-black uppercase">

                        {{ $subscription->payment_bank }}

                    </p>


                </div>




                <div class="mt-4 bg-white rounded-xl p-4">


                    <p class="text-xs text-slate-400">
                        Nomor Virtual Account
                    </p>



                    <div class="flex justify-between items-center">


                        <p
                            id="va-number"
                            class="font-black text-xl">

                            {{ $subscription->va_number }}

                        </p>



                        <button
                            onclick="copyVA()"
                            class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600">


                            <i class="fa-solid fa-copy"></i>


                        </button>


                    </div>


                </div>


            </div>


        </div>


    </div>







    {{-- CARA PEMBAYARAN --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">


        <h6 class="font-bold">

            <i class="fa-solid fa-circle-info"></i>

            Cara Pembayaran

        </h6>


        <ol class="mt-3 text-sm text-slate-500 space-y-1">


            <li>1. Buka mobile banking / ATM.</li>

            <li>2. Pilih menu Virtual Account.</li>

            <li>3. Masukkan nomor VA.</li>

            <li>4. Pastikan nominal sesuai.</li>

            <li>5. Konfirmasi pembayaran.</li>


        </ol>


    </div>





    <div class="mt-5 text-center text-xs text-slate-400">

        <i class="fa-solid fa-rotate"></i>

        Status pembayaran akan diperiksa otomatis.

    </div>





    <a href="{{ route('dashboard') }}"
        class="mt-5 flex justify-center py-3 rounded-xl bg-slate-900 text-white font-bold">

        <i class="fa-solid fa-arrow-left mr-2"></i>

        Kembali

    </a>




</div>





{{-- DATA UNTUK JS --}}
<div
    id="payment-status"
    data-url="{{ route(
        'public.subscription.payment.bank.status',
        encryptId($subscription->id)
    ) }}">
</div>





<script src="{{ asset('js/subscription/payment.js') }}"></script>


@endsection