@extends('layouts.admin')

@section('content')
    <div class="space-y-6">

        {{-- ==========================================================
        HEADER
    =========================================================== --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-black text-slate-800">
                    Rekening Merchant
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Kelola rekening bank yang digunakan merchant untuk penarikan saldo.
                </p>
            </div>


            <a href="{{ route('super_admin.merchant_bank_accounts.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl
                   bg-amber-500 text-slate-950 font-bold text-sm
                   hover:bg-amber-400 transition shadow-lg shadow-amber-500/20">

                <i class="fa-solid fa-plus"></i>

                <span>Tambah Rekening</span>

            </a>

        </div>


        {{-- ==========================================================
        FLASH MESSAGE
    =========================================================== --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 px-4 py-3 rounded-xl
                   bg-emerald-50 border border-emerald-200 text-emerald-700">

                <i class="fa-solid fa-circle-check"></i>

                <span class="text-sm font-semibold">
                    {{ session('success') }}
                </span>

            </div>
        @endif


        @if (session('error'))
            <div
                class="flex items-center gap-3 px-4 py-3 rounded-xl
                   bg-rose-50 border border-rose-200 text-rose-700">

                <i class="fa-solid fa-circle-exclamation"></i>

                <span class="text-sm font-semibold">
                    {{ session('error') }}
                </span>

            </div>
        @endif


        {{-- ==========================================================
        FILTER
    =========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200
               shadow-sm p-5">

            <form method="GET" action="{{ route('super_admin.merchant_bank_accounts.index') }}"
                class="flex flex-col md:flex-row gap-3">

                {{-- SEARCH --}}
                <div class="relative flex-1">

                    <i
                        class="fa-solid fa-magnifying-glass
                           absolute left-4 top-1/2 -translate-y-1/2
                           text-slate-400 text-sm">
                    </i>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari merchant, bank, nomor rekening..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl
                           border border-slate-200
                           text-sm text-slate-700
                           focus:outline-none focus:ring-2
                           focus:ring-amber-400/40
                           focus:border-amber-400">

                </div>


                {{-- STATUS --}}
                <select name="status"
                    class="md:w-48 px-4 py-3 rounded-xl
                       border border-slate-200
                       text-sm text-slate-700
                       focus:outline-none focus:ring-2
                       focus:ring-amber-400/40
                       focus:border-amber-400">

                    <option value="">
                        Semua Status
                    </option>

                    <option value="active" @selected(request('status') === 'active')>
                        Aktif
                    </option>

                    <option value="inactive" @selected(request('status') === 'inactive')>
                        Nonaktif
                    </option>

                </select>


                {{-- BUTTON --}}
                <button type="submit"
                    class="px-5 py-3 rounded-xl
                       bg-slate-900 text-white
                       font-bold text-sm
                       hover:bg-slate-800 transition">

                    <i class="fa-solid fa-filter mr-2"></i>

                    Filter

                </button>


                {{-- RESET --}}
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('super_admin.merchant_bank_accounts.index') }}"
                        class="px-5 py-3 rounded-xl
                           border border-slate-200
                           text-slate-600
                           font-bold text-sm
                           hover:bg-slate-50
                           transition text-center">

                        Reset

                    </a>
                @endif

            </form>

        </div>


        {{-- ==========================================================
        TABLE
    =========================================================== --}}
        <div class="bg-white rounded-2xl border border-slate-200
               shadow-sm overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-slate-50 border-b border-slate-200">

                        <tr>

                            <th class="px-6 py-4 text-left font-bold text-slate-500">
                                Merchant
                            </th>

                            <th class="px-6 py-4 text-left font-bold text-slate-500">
                                Bank
                            </th>

                            <th class="px-6 py-4 text-left font-bold text-slate-500">
                                Nomor Rekening
                            </th>

                            <th class="px-6 py-4 text-left font-bold text-slate-500">
                                Nama Pemilik
                            </th>

                            <th class="px-6 py-4 text-center font-bold text-slate-500">
                                Status
                            </th>

                            <th class="px-6 py-4 text-center font-bold text-slate-500">
                                Lock
                            </th>

                            <th class="px-6 py-4 text-center font-bold text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($bankAccounts as $bankAccount)
                            <tr class="hover:bg-slate-50/70 transition">

                                {{-- MERCHANT --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                               bg-amber-50 text-amber-600
                                               flex items-center justify-center
                                               shrink-0">

                                            <i class="fa-solid fa-store"></i>

                                        </div>

                                        <div>

                                            <p class="font-bold text-slate-800">
                                                {{ $bankAccount->merchant?->name ?? '-' }}
                                            </p>

                                            <p class="text-xs text-slate-400">
                                                Merchant
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- BANK --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-2">

                                        <div
                                            class="w-8 h-8 rounded-lg
                                               bg-slate-100
                                               flex items-center justify-center">

                                            <i class="fa-solid fa-building-columns text-slate-500"></i>

                                        </div>

                                        <span class="font-semibold text-slate-700">
                                            {{ $bankAccount->bank_name }}
                                        </span>

                                    </div>

                                </td>


                                {{-- ACCOUNT NUMBER --}}
                                <td class="px-6 py-4">

                                    <span class="font-mono font-semibold text-slate-700">

                                        {{ $bankAccount->account_number }}

                                    </span>

                                </td>


                                {{-- ACCOUNT NAME --}}
                                <td class="px-6 py-4">

                                    <span class="font-semibold text-slate-700">
                                        {{ $bankAccount->account_name }}
                                    </span>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-6 py-4 text-center">

                                    @if ($bankAccount->status === 'active')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                               px-3 py-1.5 rounded-full
                                               bg-emerald-50
                                               text-emerald-700
                                               border border-emerald-200
                                               text-xs font-bold">

                                            <span
                                                class="w-1.5 h-1.5 rounded-full
                                                   bg-emerald-500">
                                            </span>

                                            Aktif

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5
                                               px-3 py-1.5 rounded-full
                                               bg-slate-100
                                               text-slate-500
                                               border border-slate-200
                                               text-xs font-bold">

                                            <span
                                                class="w-1.5 h-1.5 rounded-full
                                                   bg-slate-400">
                                            </span>

                                            Nonaktif

                                        </span>
                                    @endif

                                </td>


                                {{-- LOCK --}}
                                <td class="px-6 py-4 text-center">

                                    @if ($bankAccount->is_locked)
                                        <span title="Rekening terkunci untuk merchant"
                                            class="inline-flex items-center justify-center
                                               w-9 h-9 rounded-lg
                                               bg-amber-50 text-amber-600">

                                            <i class="fa-solid fa-lock"></i>

                                        </span>
                                    @else
                                        <span title="Rekening tidak terkunci"
                                            class="inline-flex items-center justify-center
                                               w-9 h-9 rounded-lg
                                               bg-slate-100 text-slate-500">

                                            <i class="fa-solid fa-lock-open"></i>

                                        </span>
                                    @endif

                                </td>


                                {{-- ACTION --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- EDIT --}}
                                        <a href="{{ route('super_admin.merchant_bank_accounts.edit', encryptId($bankAccount->id)) }}"
                                            title="Edit rekening"
                                            class="w-9 h-9 rounded-lg
                                               flex items-center justify-center
                                               bg-blue-50 text-blue-600
                                               hover:bg-blue-100
                                               transition">

                                            <i class="fa-solid fa-pen-to-square"></i>

                                        </a>


                                        <form method="POST"
                                            action="{{ route('super_admin.merchant_bank_accounts.destroy', encryptId($bankAccount->id)) }}"
                                            class="delete-bank-account-form">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit" title="Hapus rekening"
                                                class="w-9 h-9 rounded-lg
                                                flex items-center justify-center
                                                bg-rose-50 text-rose-600
                                                hover:bg-rose-100
                                                transition">

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-16 text-center">

                                    <div
                                        class="w-16 h-16 mx-auto mb-4
                                           rounded-2xl bg-slate-100
                                           flex items-center justify-center
                                           text-slate-400">

                                        <i class="fa-solid fa-building-columns text-2xl"></i>

                                    </div>

                                    <h3 class="font-bold text-slate-700">
                                        Belum Ada Rekening Merchant
                                    </h3>

                                    <p class="text-sm text-slate-400 mt-1">
                                        Belum ada rekening bank merchant yang terdaftar.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ======================================================
                PAGINATION
            ======================================================= --}}
            @if ($bankAccounts->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">

                    {{ $bankAccounts->links() }}

                </div>
            @endif

        </div>

    </div>
    @push('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.delete-bank-account-form')
            .forEach(function (form) {

                form.addEventListener('submit', function (e) {

                    e.preventDefault();

                    Swal.fire({
                        title: 'Hapus Rekening?',
                        text: 'Rekening merchant ini akan dihapus dan tidak dapat dikembalikan.',
                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',

                        reverseButtons: true,

                        buttonsStyling: false,

                        customClass: {
                            popup: 'rounded-2xl',
                            title: 'text-xl font-bold text-slate-800',
                            htmlContainer: 'text-sm text-slate-500',

                            confirmButton:
                                'px-5 py-2.5 rounded-xl bg-rose-600 text-white font-bold mx-1 hover:bg-rose-700',

                            cancelButton:
                                'px-5 py-2.5 rounded-xl bg-slate-200 text-slate-700 font-bold mx-1 hover:bg-slate-300'
                        }
                    }).then(function (result) {

                        if (result.isConfirmed) {

                            form.submit();

                        }

                    });

                });

            });

    });
</script>

@endpush
@endsection
