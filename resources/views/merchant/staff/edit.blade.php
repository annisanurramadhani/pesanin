@extends('layouts.app')

@section('body')
    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>
                        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight">
                            Edit Akun Staff
                        </h2>

                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Perbarui informasi akun Kasir atau Dapur kafe.
                        </p>
                    </div>

                    <a href="{{ route('merchant.staff.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-extrabold transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>

                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-8">

                <div class="max-w-2xl mx-auto">

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">

                        {{-- Header Form --}}
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-5 mb-6">

                            <div
                                class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shadow-md shadow-amber-500/20">
                                <i class="fa-solid fa-user-pen"></i>
                            </div>

                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base">
                                    Informasi Akun Staff
                                </h3>

                                <p class="text-[11px] font-medium text-slate-400">
                                    Perbarui data akun {{ $staff->name }}
                                </p>
                            </div>

                        </div>

                        {{-- Form --}}
                        <form
                            action="{{ route('merchant.staff.update', [
                                'encryptedId' => encryptId($staff->id),
                            ]) }}"
                            method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            {{-- Nama --}}
                            <div>

                                <label for="name"
                                    class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Nama Staf <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="name" name="name" value="{{ old('name', $staff->name) }}"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                    placeholder="Contoh: Budi Kasir" required>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email"
                                    class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Email Login <span class="text-red-500">*</span>
                                </label>

                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $staff->email) }}"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                    placeholder="budi@kopipst.com" required>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password"
                                    class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Password Baru
                                </label>

                                <input type="password" id="password" name="password"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">

                                <p class="text-[11px] text-slate-400 mt-2">
                                    Password minimal 6 karakter jika ingin diubah.
                                </p>
                            </div>

                            {{-- Role --}}
                            <div>
                                <label for="role"
                                    class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                    Tugas / Role <span class="text-red-500">*</span>
                                </label>

                                <select id="role" name="role"
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition cursor-pointer"
                                    required>
                                    <option value="kasir" @selected(old('role', $staff->role) === 'kasir')>
                                        Kasir (Front Office)
                                    </option>

                                    <option value="dapur" @selected(old('role', $staff->role) === 'dapur')>
                                        Dapur (Kitchen Staff)
                                    </option>
                                </select>
                            </div>

                            {{-- Action --}}
                            <div class="flex items-center justify-end gap-3 pt-4">

                                <a href="{{ route('merchant.staff.index') }}"
                                    class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-extrabold transition">
                                    Batal
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-3 px-5 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-amber-500/25 active:scale-[0.98]">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Simpan Perubahan</span>
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </main>
        </div>
    </div>
@endsection
