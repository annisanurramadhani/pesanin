@extends('layouts.app')

@section('body')
    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            {{-- Header --}}
            <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                            Kelola Akun Staff Kafe
                        </h2>

                        <p class="text-xs font-medium text-slate-500 mt-1">
                            Atur hak akses login untuk akun Kasir dan Dapur kafe.
                        </p>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-8">

                <div class="space-y-8">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        {{-- Form Tambah Staff --}}
                        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
                            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-lg shadow-md shadow-amber-500/20">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>

                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">
                                        Tambah Staf Baru
                                    </h3>

                                    <p class="text-[11px] font-medium text-slate-400">
                                        Buat akses login kasir/dapur
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('merchant.staff.store') }}" method="POST" class="space-y-5">
                                @csrf

                                {{-- Nama --}}
                                <div>
                                    <label for="name"
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                        Nama Staf <span class="text-red-500">*</span>
                                    </label>

                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                        placeholder="Contoh: Budi Kasir" required>
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email"
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                        Email Login <span class="text-red-500">*</span>
                                    </label>

                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                        placeholder="budi@gmail.com" required>
                                </div>

                                {{-- Password --}}
                                <div>
                                    <label for="password"
                                        class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                                        Password <span class="text-red-500">*</span>
                                    </label>

                                    <input type="password" id="password" name="password"
                                        class="w-full bg-slate-50 border border-slate-200 focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 rounded-xl text-sm p-3.5 text-slate-800 font-semibold transition placeholder:font-normal placeholder:text-slate-400"
                                        placeholder="Minimal 6 karakter" required>
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
                                        <option value="kasir" @selected(old('role') === 'kasir')>
                                            Kasir (Front Office)
                                        </option>

                                        <option value="dapur" @selected(old('role') === 'dapur')>
                                            Dapur (Kitchen Staff)
                                        </option>
                                    </select>
                                </div>

                                {{-- Submit --}}
                                <button type="submit"
                                    class="w-full bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold py-4 px-4 rounded-xl text-sm transition-all duration-200 shadow-lg shadow-amber-500/25 active:scale-[0.98] flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-plus font-extrabold"></i>
                                    <span>Simpan Akun Staf</span>
                                </button>
                            </form>
                        </div>

                        {{-- Daftar Staff --}}
                        <div
                            class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

                            {{-- Table Header --}}
                            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg">
                                        Daftar Akun Staf Aktif
                                    </h3>

                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Daftar karyawan yang memiliki hak akses ke sistem kafe
                                    </p>
                                </div>

                                <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold">
                                    Total: {{ count($staffs) }} Akun
                                </span>
                            </div>

                            {{-- Table --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">

                                    <thead
                                        class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider bg-slate-50/80 border-b border-slate-100">
                                        <tr>
                                            <th class="p-4 pl-6">
                                                Nama Staf
                                            </th>

                                            <th class="p-4">
                                                Email
                                            </th>

                                            <th class="p-4">
                                                Role
                                            </th>

                                            <th class="p-4 pr-6 text-center">
                                                Aksi
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100">

                                        @forelse ($staffs as $staff)
                                            <tr class="hover:bg-slate-50/60 transition">

                                                {{-- Nama --}}
                                                <td class="p-4 pl-6">
                                                    <div class="flex items-center gap-3">

                                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-extrabold text-xs flex-shrink-0">

                                                            @php
                                                                $nameParts = preg_split('/\s+/', trim(strip_tags($staff->name)));

                                                                if (count($nameParts) >= 2) {
                                                                    $initials = strtoupper(
                                                                        substr($nameParts[0], 0, 1) .
                                                                        substr($nameParts[count($nameParts) - 1], 0, 1)
                                                                    );
                                                                } else {
                                                                    $initials = strtoupper(substr($nameParts[0], 0, 1));
                                                                }
                                                            @endphp

                                                            {{ $initials }}

                                                        </div>

                                                        <span class="font-extrabold text-slate-900 text-sm">
                                                            {{ strip_tags($staff->name) }}
                                                        </span>

                                                    </div>
                                                </td>

                                                {{-- Email --}}
                                                <td class="p-4 text-xs font-bold text-slate-600">
                                                    {{ strip_tags($staff->email) }}
                                                </td>

                                                {{-- Role --}}
                                                <td class="p-4">
                                                    @if (strtolower($staff->role) === 'kasir')
                                                        <span
                                                            class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200/80 font-extrabold rounded-lg text-xs inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-cash-register text-[10px]"></i>
                                                            Kasir
                                                        </span>
                                                    @elseif (strtolower($staff->role) === 'dapur')
                                                        <span
                                                            class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200/80 font-extrabold rounded-lg text-xs inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-utensils text-[10px]"></i>
                                                            Dapur
                                                        </span>
                                                    @else
                                                        <span
                                                            class="px-3 py-1 bg-slate-100 text-slate-700 font-extrabold rounded-lg text-xs capitalize">
                                                            {{ $staff->role }}
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- Aksi --}}
                                                {{-- Aksi --}}
                                                <td class="p-4 pr-6 text-center">
                                                    <div class="flex items-center justify-center gap-2">

                                                        {{-- Edit --}}
                                                        <a href="{{ route('merchant.staff.edit', [
                                                            'encryptedId' => encryptId($staff->id),
                                                        ]) }}"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white rounded-xl text-xs font-extrabold transition border border-amber-200"
                                                            title="Edit Staf">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                            <span>Edit</span>
                                                        </a>

                                                        {{-- Hapus --}}
                                                        <form action="{{ route('merchant.staff.destroy', $staff->id) }}"
                                                            method="POST" class="delete-staff-form inline"
                                                            data-staff-name="{{ $staff->name }}">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl text-xs font-extrabold transition border border-rose-200"
                                                                title="Hapus Staf">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                                <span>Hapus</span>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="4" class="p-12 text-center">
                                                    <div
                                                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3 text-xl">
                                                        <i class="fa-solid fa-users"></i>
                                                    </div>

                                                    <p class="font-bold text-slate-700 text-sm">
                                                        Belum ada akun staf yang dibuat.
                                                    </p>

                                                    <p class="text-xs text-slate-400 mt-1">
                                                        Gunakan form di samping untuk menambahkan Kasir atau Dapur baru.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/merchant/staf.js') }}"></script>
@endpush
