@extends('layouts.merchant')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/merchant/finance.css') }}">
@endpush

@section('header')
    <div>

        <h1 class="text-2xl font-black text-slate-800">
            Keuangan
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Kelola saldo, transaksi, dan laporan keuangan merchant
        </p>

    </div>
@endsection



@section('content')
    <div class="space-y-6">


        {{-- ================= SUMMARY ================= --}}

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


            <div class="finance-card">

                <p class="finance-label">
                    Saldo Tersedia
                </p>

                <h2 id="walletBalance" class="text-3xl font-black text-slate-800 mt-2">

                    Rp {{ number_format($balance, 0, ',', '.') }}

                </h2>


                <p class="text-sm text-slate-500">
                    Saldo yang dapat ditarik
                </p>


            </div>



            <div class="finance-card">


                <p class="finance-label">
                    Total Pendapatan
                </p>


                <h2 id="totalIncome" class="text-3xl font-black text-emerald-600 mt-2">

                    Rp {{ number_format($totalIncome, 0, ',', '.') }}

                </h2>


                <p class="text-sm text-slate-500">
                    Total pembayaran masuk
                </p>


            </div>




            <div class="finance-card">


                <p class="finance-label">
                    Total Penarikan
                </p>


                <h2 id="totalWithdraw" class="text-3xl font-black text-rose-600 mt-2">

                    Rp {{ number_format($totalWithdraw, 0, ',', '.') }}

                </h2>


                <p class="text-sm text-slate-500">
                    Saldo yang sudah ditarik
                </p>


            </div>



        </div>





        {{-- ================= HEADER ================= --}}


        <div class="flex justify-between items-center">


            <h2 class="text-xl font-black text-slate-800">
                Laporan Transaksi
            </h2>



            <div class="flex gap-3">


                @if ($bankAccount)
                    <button onclick="openWithdrawModal()" class="finance-btn">


                        <i class="fa-solid fa-money-bill-transfer mr-2"></i>

                        Tarik Saldo


                    </button>
                @else
                    <button onclick="openBankModal()" class="finance-btn">


                        <i class="fa-solid fa-building-columns mr-2"></i>

                        Tambah Rekening


                    </button>
                @endif




                <a href="{{ route('merchant.finance.pdf') }}" class="finance-btn-dark">


                    <i class="fa-solid fa-file-pdf mr-2"></i>

                    Export PDF


                </a>



            </div>


        </div>





        {{-- ================= FILTER ================= --}}


        <div class="bg-white border rounded-2xl p-4">


            <form method="GET" class="flex gap-3">


                <input type="date" name="date" value="{{ request('date') }}" class="h-10 border rounded-xl px-4">



                <select name="sort" class="h-10 border rounded-xl px-4">


                    <option value="desc">
                        Terbaru
                    </option>


                    <option value="asc">
                        Terlama
                    </option>


                </select>



                <button class="px-5 rounded-xl bg-slate-900 text-white font-bold">

                    Filter

                </button>



                <a href="{{ route('merchant.finance.index') }}" class="flex items-center px-4">

                    Reset

                </a>


            </form>


        </div>





        {{-- ================= BANK ACCOUNT ================= --}}


        <div class="bg-white border rounded-2xl p-5">


            <p class="finance-label">
                Rekening Penarikan
            </p>



            @if ($bankAccount)
                <div class="mt-4">


                    <h3 class="text-xl font-black">

                        {{ $bankAccount->bank_name }}

                    </h3>



                    <p>
                        {{ $bankAccount->account_number }}
                    </p>



                    <p>
                        a.n {{ $bankAccount->account_name }}
                    </p>




                    <div class="mt-5 flex gap-3 items-center">


                        <span class="status-lock">

                            <i class="fa-solid fa-lock"></i>

                            Terkunci

                        </span>



                        <span class="text-xs text-slate-400 font-bold">

                            Jika ingin mengganti rekening,
                            hubungi Super Admin

                        </span>



                    </div>


                </div>
            @else
                <div class="bank-empty">


                    <h3 class="text-lg font-black">

                        Rekening Penarikan Belum Tersedia

                    </h3>


                    <p class="mt-2 text-sm">

                        Tambahkan rekening bank untuk menerima pencairan saldo merchant.

                    </p>



                    <button onclick="openBankModal()" class="finance-btn mt-5">


                        <i class="fa-solid fa-building-columns mr-2"></i>

                        Tambah Rekening Bank


                    </button>


                </div>
            @endif



        </div>






        {{-- ================= TRANSAKSI ================= --}}



        <div class="bg-white border rounded-2xl overflow-hidden">


            <table class="w-full text-sm">


                <thead class="bg-slate-50">


                    <tr>


                        <th class="p-4 text-left">
                            Tanggal
                        </th>


                        <th class="p-4 text-left">
                            Keterangan
                        </th>


                        <th class="p-4 text-center">
                            Jenis
                        </th>


                        <th class="p-4 text-right">
                            Nominal
                        </th>


                    </tr>


                </thead>



                <tbody id="transactionBody">



                    @foreach ($transactions as $transaction)
                        <tr class="border-b">


                            <td class="p-4">

                                {{ $transaction->created_at->format('d M Y H:i') }}

                            </td>



                            <td class="p-4 font-bold">

                                {{ $transaction->description }}

                            </td>



                            <td class="p-4 text-center">


                                @if ($transaction->type == 'credit')
                                    <span class="badge-success">

                                        Pemasukan

                                    </span>
                                @else
                                    <span class="badge-danger">

                                        Penarikan

                                    </span>
                                @endif


                            </td>




                            <td class="p-4 text-right font-black">

                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}

                            </td>



                        </tr>
                    @endforeach



                </tbody>


            </table>


        </div>


    </div>





    {{-- ================= MODAL TAMBAH REKENING ================= --}}



    @if (!$bankAccount)
        <div id="bankModal" class="hidden">


            <div class="modal-box">


                <h3 class="text-xl font-black mb-5">

                    Tambah Rekening Bank

                </h3>



                <form method="POST" action="{{ route('merchant.bank-account.store') }}">


                    @csrf



                    <label>
                        Bank
                    </label>


                    <select name="bank_name" required class="input">

                        <option value="">
                            Pilih Bank
                        </option>

                        <option value="BCA">
                            BCA
                        </option>

                        <option value="BRI">
                            BRI
                        </option>

                        <option value="BNI">
                            BNI
                        </option>

                        <option value="MANDIRI">
                            Mandiri
                        </option>

                    </select>




                    <label>
                        Nomor Rekening
                    </label>


                    <input type="text" name="account_number" required class="input">





                    <label>
                        Nama Pemilik Rekening
                    </label>


                    <input type="text" name="account_name" required class="input">





                    <div class="flex gap-3 mt-6">


                        <button type="button" onclick="closeBankModal()" class="btn-cancel">

                            Batal

                        </button>



                        <button class="finance-btn flex-1">

                            Simpan

                        </button>


                    </div>


                </form>


            </div>


        </div>
    @endif
    {{-- ================= MODAL WITHDRAW ================= --}}


@if($bankAccount)


<div id="withdrawModal" class="hidden">


    <div class="modal-box">


        <h3 class="text-xl font-black mb-5">

            Tarik Saldo Merchant

        </h3>



        <form method="POST"
            action="{{ route('merchant.withdrawals.store') }}">


            @csrf



            <label class="font-bold text-sm">
                Jumlah Penarikan
            </label>


            <input 
                type="number"
                name="amount"
                max="{{ $balance }}"
                required
                class="input"
                placeholder="Masukkan nominal">



            <p class="text-xs text-slate-400 mt-2">

                Saldo tersedia:
                Rp {{ number_format($balance,0,',','.') }}

            </p>




            <div class="flex gap-3 mt-6">


                <button 
                    type="button"
                    onclick="closeWithdrawModal()"
                    class="btn-cancel">

                    Batal

                </button>




                <button
                    type="submit"
                    class="finance-btn flex-1">

                    Ajukan Penarikan

                </button>



            </div>


        </form>


    </div>


</div>


@endif
@endsection



@push('scripts')
    <script src="{{ asset('js/merchant/finance.js') }}"></script>
@endpush
