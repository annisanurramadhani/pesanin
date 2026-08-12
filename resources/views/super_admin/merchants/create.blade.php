@extends('layouts.admin')

@section('header')

<div class="flex items-center gap-4">

    <a
        href="{{ route('super_admin.merchants.index') }}"
        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800"
    >
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <div>

        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Tambah Merchant
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Tambahkan merchant baru ke dalam platform PesanIn.
        </p>

    </div>

</div>

@endsection

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <i class="fa-solid fa-store"></i>
                </div>

                <div>

                    <h2 class="font-extrabold text-slate-900">
                        Informasi Merchant
                    </h2>

                    <p class="text-xs text-slate-400">
                        Lengkapi informasi merchant yang akan didaftarkan.
                    </p>

                </div>

            </div>

        </div>

        <form
            id="merchantForm"
            action="{{ route('super_admin.merchants.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-6"
        >

            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <div class="md:col-span-2">

                    <label
                        for="name"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Nama Merchant
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Kopi PST"
                        autofocus
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div>

                    <label
                        for="phone"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        maxlength="20"
                        inputmode="numeric"
                        placeholder="08xxxxxxxxxx"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                    @error('phone')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div>

                    <label
                        for="status"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Status Merchant
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>

                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div class="md:col-span-2">

                    <label
                        for="address"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Alamat Merchant
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="4"
                        placeholder="Masukkan alamat lengkap merchant..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >{{ old('address') }}</textarea>

                    @error('address')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                <div class="md:col-span-2">

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                        <div class="flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div>

                                <p class="text-sm font-extrabold text-slate-800">
                                    Informasi Langganan
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    Paket dan masa langganan merchant dapat diatur setelah merchant berhasil dibuat melalui menu Kelola Langganan.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                <a
                    href="{{ route('super_admin.merchants.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-store"></i>
                    Simpan Merchant
                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')

<script src="{{ asset('js/super_admin/merchant.js') }}"></script>

@endpush
