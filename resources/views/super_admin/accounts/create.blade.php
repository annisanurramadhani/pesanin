@extends('layouts.admin')

@section('header')

    <div class="flex items-center gap-4">

        <a
            href="{{ route('super_admin.accounts.index') }}"
            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800"
        >
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <div>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Tambah Akun
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Tambahkan akun pengguna baru ke platform PesanIn.
            </p>

        </div>

    </div>

@endsection

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Header Form --}}
        <div class="border-b border-slate-200 px-6 py-5">

            <div class="flex items-center gap-3">

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500"
                >
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <div>

                    <h2 class="font-extrabold text-slate-900">
                        Informasi Akun
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Isi data akun dengan lengkap dan benar.
                    </p>

                </div>

            </div>

        </div>


        {{-- Form --}}
        <form
            action="{{ route('super_admin.accounts.store') }}"
            method="POST"
            class="p-6"
            novalidate
        >

            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


                {{-- Merchant --}}
                <div class="md:col-span-2">

                    <label
                        for="merchant_id"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Merchant
                    </label>

                    <select
                        id="merchant_id"
                        name="merchant_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                        <option value="">
                            Pilih Merchant
                        </option>

                        @foreach ($merchants as $merchant)

                            <option
                                value="{{ $merchant->id }}"
                                {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}
                            >
                                {{ $merchant->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('merchant_id')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Nama --}}
                <div>

                    <label
                        for="name"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Kasir Kopi PST"
                        maxlength="255"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                    @error('name')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Email --}}
                <div>

                    <label
                        for="email"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="contoh@kopipst.id"
                        maxlength="255"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                    @error('email')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Password --}}
                <div>

                    <label
                        for="password"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Konfirmasi Password --}}
                <div>

                    <label
                        for="password_confirmation"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ulangi password"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                </div>


                {{-- Role --}}
                <div>

                    <label
                        for="role"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Role
                    </label>

                    <select
                        id="role"
                        name="role"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                        <option value="">
                            Pilih Role
                        </option>

                        <option
                            value="owner"
                            {{ old('role') === 'owner' ? 'selected' : '' }}
                        >
                            Owner
                        </option>

                        <option
                            value="kasir"
                            {{ old('role') === 'kasir' ? 'selected' : '' }}
                        >
                            Kasir
                        </option>

                        <option
                            value="dapur"
                            {{ old('role') === 'dapur' ? 'selected' : '' }}
                        >
                            Dapur
                        </option>

                    </select>

                    @error('role')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Status --}}
                <div>

                    <label
                        for="status"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                        <option
                            value="active"
                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}
                        >
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            {{ old('status') === 'inactive' ? 'selected' : '' }}
                        >
                            Nonaktif
                        </option>

                    </select>

                    @error('status')
                        <p class="mt-1 text-xs font-semibold text-rose-500">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            {{-- Button --}}
            <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                <a
                    href="{{ route('super_admin.accounts.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400"
                >
                    <i class="fa-solid fa-user-plus"></i>
                    Simpan Akun
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
