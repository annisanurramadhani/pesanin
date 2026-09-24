@extends('layouts.admin')

@section('header')
    <div>
        <h1 class="text-2xl font-black text-slate-800">
            Penarikan Saldo Merchant
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola permintaan pencairan saldo merchant
        </p>
    </div>
@endsection


@section('content')

    <div class="space-y-6">

        {{-- ==========================================================
            DAFTAR WITHDRAWAL
        =========================================================== --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- HEADER --}}
            <div class="border-b border-slate-100 px-6 py-5">

                <h2 class="text-lg font-black text-slate-800">
                    Daftar Withdrawal
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Kelola permintaan penarikan saldo dari merchant.
                </p>

            </div>


            {{-- ======================================================
                TABLE
            ======================================================= --}}
            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    {{-- TABLE HEADER --}}
                    <thead class="bg-slate-50">

                        <tr>

                            <th
                                class="whitespace-nowrap px-6 py-4 text-left font-black text-slate-500"
                            >
                                Merchant
                            </th>

                            <th
                                class="whitespace-nowrap px-6 py-4 text-left font-black text-slate-500"
                            >
                                Nominal
                            </th>

                            <th
                                class="whitespace-nowrap px-6 py-4 text-left font-black text-slate-500"
                            >
                                Rekening
                            </th>

                            <th
                                class="whitespace-nowrap px-6 py-4 text-center font-black text-slate-500"
                            >
                                Status
                            </th>

                            <th
                                class="whitespace-nowrap px-6 py-4 text-left font-black text-slate-500"
                            >
                                Payout
                            </th>

                            <th
                                class="min-w-[220px] whitespace-nowrap px-6 py-4 text-center font-black text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    {{-- ==================================================
                        TABLE BODY
                    =================================================== --}}
                    <tbody
                        id="withdrawalTableBody"
                        data-realtime-url="{{ route('super_admin.withdrawals.realtime') }}"
                        data-approve-url-template="{{ route('super_admin.withdrawals.approve', ['withdrawal' => '__ID__']) }}"
                        data-reject-url-template="{{ route('super_admin.withdrawals.reject', ['withdrawal' => '__ID__']) }}"
                    >

                        @forelse($withdrawals as $withdrawal)

                            <tr
                                data-withdrawal-id="{{ $withdrawal->id }}"
                                data-status="{{ $withdrawal->status }}"
                                class="withdrawal-row border-b border-slate-100 transition-colors hover:bg-slate-50"
                            >

                                {{-- ==================================================
                                    MERCHANT
                                =================================================== --}}
                                <td class="withdrawal-merchant px-6 py-5">

                                    <p class="font-black text-slate-800">
                                        {{ $withdrawal->merchant->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        #WD-{{ $withdrawal->id }}
                                    </p>

                                </td>


                                {{-- ==================================================
                                    NOMINAL
                                =================================================== --}}
                                <td class="withdrawal-amount px-6 py-5">

                                    <span class="font-black text-slate-800">
                                        Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                    </span>

                                </td>


                                {{-- ==================================================
                                    REKENING
                                =================================================== --}}
                                <td class="withdrawal-bank px-6 py-5">

                                    @if($withdrawal->bankAccount)

                                        <p class="font-bold text-slate-700">
                                            {{ $withdrawal->bankAccount->bank_name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $withdrawal->bankAccount->account_number }}
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            a.n {{ $withdrawal->bankAccount->account_name }}
                                        </p>

                                    @else

                                        <span class="text-slate-400">
                                            Rekening tidak ditemukan
                                        </span>

                                    @endif

                                </td>


                                {{-- ==================================================
                                    STATUS
                                =================================================== --}}
                                <td class="withdrawal-status px-6 py-5 text-center">

                                    @if($withdrawal->status === 'pending')

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-black text-amber-700"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>

                                    @elseif($withdrawal->status === 'processing')

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-black text-blue-700"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                            Diproses
                                        </span>

                                    @elseif($withdrawal->status === 'paid')

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black text-emerald-700"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Berhasil
                                        </span>

                                    @elseif($withdrawal->status === 'failed')

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-black text-red-700"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Gagal
                                        </span>

                                    @elseif($withdrawal->status === 'rejected')

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1.5 text-xs font-black text-rose-700"
                                        >
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>

                                    @else

                                        <span class="text-xs font-bold text-slate-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- ==================================================
                                    PAYOUT
                                =================================================== --}}
                                <td class="withdrawal-payout px-6 py-5 text-xs text-slate-500">

                                    <span>
                                        {{ $withdrawal->payout_status ?? '-' }}
                                    </span>

                                    @if($withdrawal->payout_id)

                                        <p class="mt-1 break-all text-[10px] text-slate-400">
                                            {{ $withdrawal->payout_id }}
                                        </p>

                                    @endif

                                    @if(
                                        $withdrawal->status === 'failed' &&
                                        data_get($withdrawal->payout_response, 'message')
                                    )

                                        <p class="mt-1 text-red-600">
                                            {{ data_get($withdrawal->payout_response, 'message') }}
                                        </p>

                                    @endif

                                    @if($withdrawal->note)

                                        <p class="mt-1 text-rose-600">
                                            {{ $withdrawal->note }}
                                        </p>

                                    @endif

                                </td>


                                {{-- ==================================================
                                    AKSI
                                =================================================== --}}
                                <td class="withdrawal-action px-6 py-5 text-center">

                                    @if($withdrawal->status === 'pending')

                                        <div class="flex w-full min-w-[220px] items-center justify-center gap-2">

                                            {{-- SETUJUI --}}
                                            <form
                                                method="POST"
                                                action="{{ route('super_admin.withdrawals.approve', $withdrawal->id) }}"
                                                class="flex-1"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-emerald-600 active:scale-[0.98]"
                                                >
                                                    <i class="fa-solid fa-check text-[11px]"></i>
                                                    Setujui
                                                </button>

                                            </form>


                                            {{-- TOLAK --}}
                                            <button
                                                type="button"
                                                class="withdrawal-reject-trigger flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-rose-500 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-rose-600 active:scale-[0.98]"
                                                data-withdrawal-id="{{ $withdrawal->id }}"
                                                data-merchant-name="{{ $withdrawal->merchant->name ?? '-' }}"
                                                data-amount="{{ number_format($withdrawal->amount, 0, ',', '.') }}"
                                            >
                                                <i class="fa-solid fa-xmark text-[11px]"></i>
                                                Tolak
                                            </button>

                                        </div>

                                    @elseif($withdrawal->status === 'paid')

                                        <div class="flex flex-col items-center gap-1">

                                            <span class="inline-flex items-center gap-1.5 text-xs font-black text-emerald-600">
                                                <i class="fa-solid fa-circle-check"></i>
                                                Selesai
                                            </span>

                                            @if($withdrawal->payout_id)

                                                <p class="max-w-[220px] break-all text-[10px] text-slate-400">
                                                    {{ $withdrawal->payout_id }}
                                                </p>

                                            @endif

                                        </div>

                                    @elseif($withdrawal->status === 'processing')

                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-blue-600">
                                            <i class="fa-solid fa-spinner"></i>
                                            Menunggu payout
                                        </span>

                                    @elseif($withdrawal->status === 'rejected')

                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-rose-600">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            Ditolak
                                        </span>

                                    @elseif($withdrawal->status === 'failed')

                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-red-600">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            Gagal
                                        </span>

                                    @else

                                        <span class="text-xs font-black text-slate-400">
                                            Selesai
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr class="withdrawal-empty-row">

                                <td
                                    colspan="6"
                                    class="py-12 text-center text-slate-400"
                                >

                                    <div class="flex flex-col items-center justify-center gap-2">

                                        <i class="fa-solid fa-money-bill-transfer text-2xl text-slate-300"></i>

                                        <span class="text-sm">
                                            Belum ada permintaan penarikan
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
            PAGINATION
        =========================================================== --}}
        @if($withdrawals->hasPages())

            <div>
                {{ $withdrawals->links() }}
            </div>

        @endif

    </div>


    {{-- ==============================================================
        MODAL TOLAK WITHDRAWAL
    ============================================================== --}}
    <div
        id="rejectWithdrawalModal"
        class="fixed inset-0 z-[9999] hidden"
        aria-hidden="true"
    >

        {{-- BACKDROP --}}
        <div
            id="rejectWithdrawalBackdrop"
            class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"
        ></div>


        {{-- MODAL --}}
        <div class="relative flex min-h-screen items-center justify-center p-4">

            <div
                id="rejectWithdrawalDialog"
                class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
            >

                {{-- ==================================================
                    HEADER
                =================================================== --}}
                <div class="px-6 pt-6">

                    <div class="flex items-start justify-between gap-4">

                        <div class="flex items-start gap-4">

                            {{-- ICON --}}
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-500">

                                <i class="fa-solid fa-xmark text-xl"></i>

                            </div>


                            <div>

                                <h3 class="text-lg font-black text-slate-800">
                                    Tolak Penarikan Saldo
                                </h3>

                                <p class="mt-1 text-sm font-medium text-slate-500">
                                    Yakin ingin menolak penarikan saldo ini?
                                </p>

                                <p class="mt-2 max-w-md text-xs leading-5 text-slate-400">
                                    Masukkan alasan penolakan dengan jelas agar
                                    merchant dapat memahami penyebabnya.
                                </p>

                            </div>

                        </div>


                        {{-- CLOSE --}}
                        <button
                            type="button"
                            id="closeRejectWithdrawalModal"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                            aria-label="Tutup"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>

                    </div>

                </div>


                {{-- ==================================================
                    WITHDRAWAL INFO
                =================================================== --}}
                <div class="px-6 pt-5">

                    <div class="flex items-center justify-between gap-4 rounded-xl bg-slate-50 px-4 py-4">

                        <div class="flex min-w-0 items-center gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm">

                                <i class="fa-solid fa-store"></i>

                            </div>


                            <div class="min-w-0">

                                <p
                                    id="rejectWithdrawalMerchant"
                                    class="truncate text-sm font-black text-slate-800"
                                >
                                    -
                                </p>

                                <p
                                    id="rejectWithdrawalNumber"
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    -
                                </p>

                            </div>

                        </div>


                        <div class="shrink-0 text-right">

                            <p
                                id="rejectWithdrawalAmount"
                                class="text-base font-black text-slate-800"
                            >
                                Rp 0
                            </p>

                            <p class="text-[10px] font-medium text-slate-400">
                                Nominal Penarikan
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ==================================================
                    FORM PENOLAKAN
                =================================================== --}}
                <form
                    id="rejectWithdrawalForm"
                    method="POST"
                    class="px-6 pb-6 pt-5"
                >

                    @csrf

                    <div>

                        <div class="mb-2 flex items-center justify-between">

                            <label
                                for="rejectWithdrawalNote"
                                class="text-sm font-bold text-slate-700"
                            >
                                Alasan Penolakan
                                <span class="text-rose-500">*</span>
                            </label>

                            <span
                                id="rejectWithdrawalCounter"
                                class="text-[11px] font-medium text-slate-400"
                            >
                                0/1000 karakter
                            </span>

                        </div>


                        <textarea
                            id="rejectWithdrawalNote"
                            name="note"
                            required
                            maxlength="1000"
                            rows="5"
                            placeholder="Tuliskan alasan penolakan secara jelas..."
                            class="block w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 shadow-sm outline-none transition focus:border-rose-400 focus:ring-4 focus:ring-rose-100"
                        ></textarea>


                        <p class="mt-2 text-[11px] text-slate-400">
                            Alasan ini akan tersimpan sebagai catatan penolakan withdrawal merchant.
                        </p>

                    </div>


                    {{-- BUTTON --}}
                    <div class="mt-6 flex items-center justify-end gap-3">

                        <button
                            type="button"
                            id="cancelRejectWithdrawal"
                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-xs font-black text-slate-600 shadow-sm transition hover:bg-slate-50 active:scale-[0.98]"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            id="submitRejectWithdrawal"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-rose-500 px-5 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-rose-600 active:scale-[0.98]"
                        >
                            <i class="fa-solid fa-xmark text-[11px]"></i>
                            Tolak Penarikan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection


{{-- ==============================================================
    JAVASCRIPT
============================================================== --}}
@push('scripts')

    <script
        src="{{ asset('js/super_admin/withdrawals.js') }}?v={{ filemtime(public_path('js/super_admin/withdrawals.js')) }}"
        defer
    ></script>

@endpush