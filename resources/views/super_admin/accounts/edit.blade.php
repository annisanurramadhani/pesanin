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
                Edit Akun
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi akun pengguna.
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
                    <i class="fa-solid fa-user-pen"></i>
                </div>

                <div>
                    <h2 class="font-extrabold text-slate-900">
                        Informasi Akun
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Perbarui data akun dengan benar.
                    </p>
                </div>

            </div>

        </div>

        <form
            action="{{ route('super_admin.accounts.update', [
                'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $user->id),
            ]) }}"
            method="POST"
            class="p-6"
        >

            @csrf
            @method('PUT')

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
                                {{ old('merchant_id', $user->merchant_id) == $merchant->id ? 'selected' : '' }}
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
                        Nama
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
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
                        value="{{ old('email', $user->email) }}"
                        placeholder="contoh@email.com"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
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
                        Password Baru
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Kosongkan jika tidak diubah"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
                    >

                    <p class="mt-1 text-xs text-slate-400">
                        Kosongkan jika password tidak ingin diubah.
                    </p>

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
                        placeholder="Ulangi password baru"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10"
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

                        <option value="owner"
                            {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>
                            Owner
                        </option>

                        <option value="kasir"
                            {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>
                            Kasir
                        </option>

                        <option value="dapur"
                            {{ old('role', $user->role) === 'dapur' ? 'selected' : '' }}>
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

                        <option value="active"
                            {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="inactive"
                            {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>
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
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
