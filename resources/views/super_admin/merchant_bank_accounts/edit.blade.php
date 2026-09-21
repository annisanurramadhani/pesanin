@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- ==========================================================
        HEADER
    =========================================================== --}}
    <div class="flex items-center gap-4">

        <a
            href="{{ route('super_admin.merchant_bank_accounts.index') }}"
            class="w-10 h-10 rounded-xl bg-white border border-slate-200
                   flex items-center justify-center
                   text-slate-500 hover:bg-slate-50 transition">

            <i class="fa-solid fa-arrow-left"></i>

        </a>

        <div>

            <h1 class="text-2xl font-black text-slate-800">
                Edit Rekening Merchant
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Perbarui informasi rekening bank merchant.
            </p>

        </div>

    </div>


    {{-- ==========================================================
        FORM
    =========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

        <form
            method="POST"
            action="{{ route(
                'super_admin.merchant_bank_accounts.update',
                encryptId($bankAccount->id)
            ) }}">

            @csrf
            @method('PUT')


            <div class="p-6 space-y-6">

                {{-- ==================================================
                    MERCHANT
                =================================================== --}}
                <div>

                    <label
                        for="merchant_id"
                        class="block text-sm font-bold text-slate-700 mb-2">

                        Merchant

                    </label>

                    <select
                        id="merchant_id"
                        name="merchant_id"
                        required
                        class="w-full px-4 py-3 rounded-xl
                               border border-slate-200
                               text-sm text-slate-700
                               focus:outline-none
                               focus:ring-2 focus:ring-amber-400/40
                               focus:border-amber-400">

                        <option value="">
                            Pilih Merchant
                        </option>

                        @foreach($merchants as $merchant)

                            <option
                                value="{{ $merchant->id }}"
                                @selected(
                                    old(
                                        'merchant_id',
                                        $bankAccount->merchant_id
                                    ) == $merchant->id
                                )>

                                {{ $merchant->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('merchant_id')

                        <p class="mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ==================================================
                    BANK
                =================================================== --}}
                <div>

                    <label
                        for="bank_name"
                        class="block text-sm font-bold text-slate-700 mb-2">

                        Bank

                    </label>

                    <select
                        id="bank_name"
                        name="bank_name"
                        required
                        class="w-full px-4 py-3 rounded-xl
                               border border-slate-200
                               text-sm text-slate-700
                               focus:outline-none
                               focus:ring-2 focus:ring-amber-400/40
                               focus:border-amber-400">

                        @php
                            $banks = [
                                'BCA',
                                'BRI',
                                'BNI',
                                'Mandiri',
                                'BSI',
                                'CIMB Niaga',
                                'BTN',
                                'Bank Jago',
                                'Permata',
                                'Danamon',
                                'OCBC',
                                'Maybank',
                            ];
                        @endphp

                        @foreach($banks as $bank)

                            <option
                                value="{{ $bank }}"
                                @selected(
                                    old(
                                        'bank_name',
                                        $bankAccount->bank_name
                                    ) === $bank
                                )>

                                {{ $bank }}

                            </option>

                        @endforeach

                    </select>

                    @error('bank_name')

                        <p class="mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ==================================================
                    NOMOR REKENING
                =================================================== --}}
                <div>

                    <label
                        for="account_number"
                        class="block text-sm font-bold text-slate-700 mb-2">

                        Nomor Rekening

                    </label>

                    <input
                        type="text"
                        id="account_number"
                        name="account_number"
                        value="{{ old(
                            'account_number',
                            $bankAccount->account_number
                        ) }}"
                        required
                        autocomplete="off"
                        class="w-full px-4 py-3 rounded-xl
                               border border-slate-200
                               text-sm text-slate-700
                               focus:outline-none
                               focus:ring-2 focus:ring-amber-400/40
                               focus:border-amber-400">

                    @error('account_number')

                        <p class="mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ==================================================
                    NAMA PEMILIK
                =================================================== --}}
                <div>

                    <label
                        for="account_name"
                        class="block text-sm font-bold text-slate-700 mb-2">

                        Nama Pemilik Rekening

                    </label>

                    <input
                        type="text"
                        id="account_name"
                        name="account_name"
                        value="{{ old(
                            'account_name',
                            $bankAccount->account_name
                        ) }}"
                        required
                        autocomplete="off"
                        class="w-full px-4 py-3 rounded-xl
                               border border-slate-200
                               text-sm text-slate-700
                               focus:outline-none
                               focus:ring-2 focus:ring-amber-400/40
                               focus:border-amber-400">

                    @error('account_name')

                        <p class="mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ==================================================
                    STATUS
                =================================================== --}}
                <div>

                    <label
                        for="status"
                        class="block text-sm font-bold text-slate-700 mb-2">

                        Status Rekening

                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                        class="w-full px-4 py-3 rounded-xl
                               border border-slate-200
                               text-sm text-slate-700
                               focus:outline-none
                               focus:ring-2 focus:ring-amber-400/40
                               focus:border-amber-400">

                        <option
                            value="active"
                            @selected(
                                old(
                                    'status',
                                    $bankAccount->status
                                ) === 'active'
                            )>

                            Aktif

                        </option>

                        <option
                            value="inactive"
                            @selected(
                                old(
                                    'status',
                                    $bankAccount->status
                                ) === 'inactive'
                            )>

                            Nonaktif

                        </option>

                    </select>

                    @error('status')

                        <p class="mt-1 text-xs text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- ==================================================
                    LOCK INFORMATION
                =================================================== --}}
                <div
                    class="flex items-start gap-3 p-4 rounded-xl
                           bg-amber-50 border border-amber-200">

                    <div
                        class="w-9 h-9 shrink-0 rounded-lg
                               bg-amber-100 text-amber-600
                               flex items-center justify-center">

                        <i class="fa-solid fa-lock"></i>

                    </div>

                    <div>

                        <p class="text-sm font-bold text-amber-800">
                            Rekening Terkunci
                        </p>

                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                            Rekening yang sudah dibuat tidak dapat diubah
                            oleh merchant. Perubahan rekening hanya dapat
                            dilakukan oleh Super Admin.
                        </p>

                    </div>

                </div>

            </div>


            {{-- ======================================================
                FOOTER BUTTON
            ======================================================= --}}
            <div
                class="px-6 py-4 bg-slate-50 border-t border-slate-200
                       flex flex-col sm:flex-row sm:justify-end gap-3">

                <a
                    href="{{ route(
                        'super_admin.merchant_bank_accounts.index'
                    ) }}"
                    class="px-5 py-3 rounded-xl
                           border border-slate-200
                           bg-white text-slate-600
                           font-bold text-sm
                           hover:bg-slate-50 transition
                           text-center">

                    Batal

                </a>

                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl
                           bg-amber-500 text-slate-950
                           font-bold text-sm
                           hover:bg-amber-400 transition
                           shadow-lg shadow-amber-500/20">

                    <i class="fa-solid fa-floppy-disk mr-2"></i>

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection