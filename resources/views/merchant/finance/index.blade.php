@extends('layouts.merchant')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/merchant/finance.css') }}">
@endpush


@section('header')
    <div>

        <h1 class="text-2xl font-black text-slate-800">
            Keuangan
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola saldo, transaksi, dan laporan keuangan merchant
        </p>

    </div>
@endsection



@section('content')

    <div class="space-y-6">


        {{-- ==========================================================
            SUMMARY
        =========================================================== --}}

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

            {{-- TOTAL PENDAPATAN --}}
            <div class="finance-card">

                <p class="finance-label">
                    Total Pendapatan
                </p>

                <h2 id="totalIncome" class="mt-2 text-3xl font-black text-emerald-600">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Total pembayaran masuk
                </p>

            </div>


            {{-- SALDO --}}
            <div class="finance-card">

                <p class="finance-label">
                    Saldo Tersedia
                </p>

                <h2 id="walletBalance" class="mt-2 text-3xl font-black text-slate-800">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Saldo yang dapat ditarik
                </p>

            </div>


            {{-- TOTAL WITHDRAW --}}
            <div class="finance-card">

                <p class="finance-label">
                    Total Penarikan
                </p>

                <h2 id="totalWithdraw" class="mt-2 text-3xl font-black text-rose-600">
                    Rp {{ number_format($totalWithdraw, 0, ',', '.') }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Saldo yang sudah ditarik
                </p>

            </div>

        </div>



        {{-- ==========================================================
            LAPORAN TRANSAKSI
        =========================================================== --}}

        <div>

            <h2 class="text-xl font-black text-slate-800">
                Laporan Transaksi
            </h2>

        </div>



        {{-- ==========================================================
            FILTER
        =========================================================== --}}

        <div class="rounded-2xl border bg-white p-4">

            <form method="GET" class="flex flex-col gap-3 md:flex-row md:flex-wrap md:items-center">

                <input type="date" name="date" value="{{ request('date') }}"
                    class="h-10 w-full rounded-xl border px-4 md:w-auto">


                <select name="sort" class="h-10 w-full rounded-xl border px-7 md:w-auto">

                    <option value="desc">
                        Terbaru
                    </option>

                    <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>
                        Terlama
                    </option>

                </select>


                <button type="submit" class="h-10 w-full rounded-xl bg-slate-900 px-5 font-bold text-white md:w-auto">
                    Filter
                </button>


                <a href="{{ route('merchant.finance.index') }}"
                    class="flex h-10 w-full items-center justify-center rounded-xl bg-slate-900 px-4 font-bold text-white md:w-auto">
                    Reset
                </a>


                <div class="flex w-full flex-col gap-3 md:ml-auto md:w-auto md:flex-row">

                    @if ($bankAccount)
                        <button type="button" onclick="openWithdrawModal()"
                            class="finance-btn w-full justify-center md:w-auto">
                            <i class="fa-solid fa-money-bill-transfer mr-2"></i>
                            Tarik Saldo
                        </button>
                    @else
                        <button type="button" onclick="openBankModal()"
                            class="finance-btn w-full justify-center md:w-auto">
                            <i class="fa-solid fa-building-columns mr-2"></i>
                            Tambah Rekening
                        </button>
                    @endif


                    <a href="{{ route('merchant.finance.pdf') }}" class="finance-btn-dark w-full justify-center md:w-auto">
                        <i class="fa-solid fa-file-pdf mr-2"></i>
                        Export PDF
                    </a>

                </div>

            </form>

        </div>



        {{-- ==========================================================
            REKENING BANK
        =========================================================== --}}

        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">

            @if ($bankAccount)
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                    {{-- INFO REKENING --}}
                    <div class="flex min-w-0 items-center gap-4">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                            <i class="fa-solid fa-building-columns"></i>

                        </div>


                        <div class="min-w-0">

                            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                                Rekening Penarikan
                            </p>


                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">

                                <h3 class="text-base font-black text-slate-800">
                                    {{ $bankAccount->bank_name }}
                                </h3>

                                <span class="text-sm font-semibold text-slate-500">
                                    {{ $bankAccount->account_number }}
                                </span>

                            </div>


                            <p class="mt-0.5 text-sm text-slate-500">
                                a.n {{ $bankAccount->account_name }}
                            </p>

                        </div>

                    </div>


                    {{-- STATUS --}}
                    <div class="flex items-center gap-3 md:shrink-0">

                        <span class="status-lock">

                            <i class="fa-solid fa-lock"></i>

                            Terkunci

                        </span>


                        @if ($websiteSetting && $websiteSetting->footer_email)
                            <a href="mailto:{{ $websiteSetting->footer_email }}?subject={{ rawurlencode('Permintaan Perubahan Rekening Bank') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-bold text-white transition hover:bg-slate-800">

                                <i class="fa-solid fa-envelope"></i>

                                Hubungi Admin

                            </a>
                        @endif

                    </div>

                </div>
            @else
                {{-- EMPTY ACCOUNT --}}
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                    <div class="flex items-center gap-4">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-500">

                            <i class="fa-solid fa-building-columns"></i>

                        </div>


                        <div>

                            <h3 class="text-base font-black text-slate-800">
                                Rekening Penarikan Belum Tersedia
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Tambahkan rekening untuk menerima pencairan saldo.
                            </p>

                        </div>

                    </div>


                    <button type="button" onclick="openBankModal()" class="finance-btn w-full justify-center md:w-auto">

                        <i class="fa-solid fa-building-columns mr-2"></i>

                        Tambah Rekening

                    </button>

                </div>
            @endif

        </div>



        {{-- ==========================================================
            RIWAYAT PENARIKAN
            REALTIME
        =========================================================== --}}

        <div id="withdrawalHistoryContainer" data-realtime-url="{{ route('merchant.finance.withdrawals.realtime') }}"
            class="overflow-hidden rounded-2xl border bg-white">

            <div class="border-b border-slate-100 px-5 py-4">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-lg font-black text-slate-800">
                            Riwayat Penarikan
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            Status penarikan diperbarui secara otomatis.
                        </p>

                    </div>


                    {{-- REALTIME INDICATOR --}}
                    <div id="withdrawalRealtimeStatus"
                        class="flex items-center gap-2 text-[11px] font-bold text-emerald-600">

                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                        Realtime

                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="p-4 text-left">
                                Tanggal
                            </th>

                            <th class="p-4 text-right">
                                Nominal
                            </th>

                            <th class="p-4 text-center">
                                Status
                            </th>

                            <th class="p-4 text-left">
                                Keterangan
                            </th>

                        </tr>

                    </thead>


                    <tbody id="withdrawalHistoryBody">

                        @forelse($withdrawals as $withdrawal)
                            <tr class="withdrawal-history-row border-b" data-withdrawal-id="{{ $withdrawal->id }}"
                                data-status="{{ $withdrawal->status }}">

                                <td class="p-4">
                                    {{ $withdrawal->created_at->format('d M Y H:i') }}
                                </td>


                                <td class="p-4 text-right font-black">

                                    Rp
                                    {{ number_format($withdrawal->amount, 0, ',', '.') }}

                                </td>


                                <td class="p-4 text-center">

                                    @if ($withdrawal->status === 'pending')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>

                                            Menunggu

                                        </span>
                                    @elseif($withdrawal->status === 'processing')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>

                                            Diproses

                                        </span>
                                    @elseif($withdrawal->status === 'paid')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Berhasil

                                        </span>
                                    @elseif($withdrawal->status === 'rejected')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-bold text-rose-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>

                                            Ditolak

                                        </span>
                                    @elseif($withdrawal->status === 'failed')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                            Gagal

                                        </span>
                                    @endif

                                </td>


                                <td class="p-4 text-slate-500">

                                    @if ($withdrawal->note)
                                        {{ $withdrawal->note }}
                                    @elseif($withdrawal->payout_status)
                                        {{ $withdrawal->payout_status }}
                                    @else
                                        Pengajuan penarikan sedang diproses.
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr id="withdrawalEmptyRow">

                                <td colspan="4" class="p-8 text-center text-slate-400">

                                    <div class="flex flex-col items-center gap-2">

                                        <i class="fa-solid fa-money-bill-transfer text-2xl text-slate-300"></i>

                                        <span>
                                            Belum ada pengajuan penarikan.
                                        </span>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>



        {{-- ==========================================================
            TRANSAKSI
        =========================================================== --}}

        <div class="overflow-hidden rounded-2xl border bg-white">

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

                                @if ($transaction->type === 'credit')
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

                                Rp
                                {{ number_format($transaction->amount, 0, ',', '.') }}

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>



    {{-- ==============================================================
        MODAL TAMBAH REKENING
    ============================================================== --}}

    @if (!$bankAccount)
        <div id="bankModal" class="hidden">

            <div class="modal-box">

                <h3 class="mb-5 text-xl font-black">
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


                    <div class="mt-6 flex gap-3">

                        <button type="button" onclick="closeBankModal()" class="btn-cancel">
                            Batal
                        </button>


                        <button type="submit" class="finance-btn flex-1">
                            Simpan
                        </button>

                    </div>

                </form>

            </div>

        </div>
    @endif



    {{-- ==============================================================
        MODAL WITHDRAWAL
    ============================================================== --}}

    @if ($bankAccount)
        <div id="withdrawModal" class="hidden">

            <div class="modal-box">

                {{-- HEADER --}}
                <div class="mb-5">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">

                            <i class="fa-solid fa-money-bill-transfer"></i>

                        </div>


                        <div>

                            <h3 class="text-xl font-black text-slate-800">
                                Tarik Saldo Merchant
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Ajukan pencairan saldo ke rekening terdaftar.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- INFORMASI PROSES --}}
                <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 p-4">

                    <div class="flex gap-3">

                        <i class="fa-solid fa-circle-info mt-0.5 text-blue-500"></i>

                        <div>

                            <p class="text-sm font-black text-blue-800">
                                Informasi proses penarikan
                            </p>

                            <p class="mt-1 text-xs leading-5 text-blue-700">

                                Pengajuan akan diverifikasi oleh admin dan
                                diproses maksimal
                                <strong>3 hari kerja</strong>.

                                Waktu pencairan dapat berbeda tergantung
                                proses bank dan hari libur.

                            </p>

                        </div>

                    </div>

                </div>


                {{-- FORM --}}
                <form method="POST" action="{{ route('merchant.withdrawals.store') }}" id="withdrawForm">

                    @csrf


                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Jumlah Penarikan
                    </label>


                    <div class="relative">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">
                            Rp
                        </span>


                        <input type="number" name="amount" id="withdrawAmount" min="1"
                            max="{{ $balance }}" required class="input w-full pl-12"
                            placeholder="Masukkan nominal">

                    </div>


                    <p class="mt-2 text-xs text-slate-400">

                        Saldo tersedia:
                        <strong class="text-slate-600">
                            Rp {{ number_format($balance, 0, ',', '.') }}
                        </strong>

                    </p>


                    {{-- REKENING --}}
                    <div class="mt-4 rounded-xl bg-slate-50 p-3">

                        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">
                            Rekening Tujuan
                        </p>

                        <p class="mt-1 text-sm font-black text-slate-800">
                            {{ $bankAccount->bank_name }}
                            -
                            {{ $bankAccount->account_number }}
                        </p>

                        <p class="text-xs text-slate-500">
                            a.n {{ $bankAccount->account_name }}
                        </p>

                    </div>


                    {{-- BUTTON --}}
                    <div class="mt-6 flex justify-end gap-3">

                        <button type="button" onclick="closeWithdrawModal()" class="btn-cancel w-32 px-5 py-3">
                            Batal
                        </button>

                        <button type="submit" id="submitWithdrawButton"
                            class="finance-btn w-44 px-5 py-3 justify-center">
                            <i class="fa-solid fa-paper-plane mr-2"></i>
                            Ajukan Penarikan
                        </button>

                    </div>

                </form>

            </div>

        </div>
    @endif

@endsection



@push('scripts')
    <script>
        window.financeConfig = {

            withdrawalRealtimeUrl: @json(route('merchant.finance.withdrawals.realtime')),

            csrfToken: @json(csrf_token()),

            hasBankAccount: @json((bool) $bankAccount),

            initialBalance: @json((float) $balance)

        };
    </script>


    <script src="{{ asset('js/merchant/finance.js') }}?v={{ filemtime(public_path('js/merchant/finance.js')) }}" defer>
    </script>
@endpush
