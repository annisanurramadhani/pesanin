@extends('layouts.admin')

@section('header')
    <div class="flex items-center justify-between">

        <div>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Kelola Akun
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola akun pengguna yang terdaftar di platform PesanIn.
            </p>

        </div>

        <a href="{{ route('super_admin.accounts.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">
            <i class="fa-solid fa-user-plus"></i>
            Tambah Akun
        </a>

    </div>
@endsection


@section('content')
    <div class="space-y-6">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


            {{-- Header --}}
            <div class="border-b border-slate-200 px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>

                        <h2 class="font-extrabold text-slate-900">
                            Daftar Akun
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            Daftar akun pengguna yang tersedia.
                        </p>

                    </div>

                </div>

            </div>


            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="border-b border-slate-200 bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 font-extrabold text-slate-600">
                                #
                            </th>

                            <th class="px-6 py-4 font-extrabold text-slate-600">
                                Nama
                            </th>

                            <th class="px-6 py-4 font-extrabold text-slate-600">
                                Email
                            </th>

                            <th class="px-6 py-4 font-extrabold text-slate-600">
                                Role
                            </th>

                            <th class="px-6 py-4 font-extrabold text-slate-600">
                                Merchant
                            </th>

                            <th class="px-6 py-4 font-extrabold text-slate-600">
                                Status
                            </th>

                            <th class="px-6 py-4 text-center font-extrabold text-slate-600">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($users as $user)
                            @php
                                $encryptedId = \Illuminate\Support\Facades\Crypt::encryptString((string) $user->id);

                                $roleClass = match ($user->role) {
                                    'super_admin' => 'bg-purple-50 text-purple-700 border-purple-200',

                                    'owner' => 'bg-blue-50 text-blue-700 border-blue-200',

                                    'kasir' => 'bg-amber-50 text-amber-700 border-amber-200',

                                    'dapur' => 'bg-emerald-50 text-emerald-700 border-emerald-200',

                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp


                            <tr class="transition hover:bg-slate-50">


                                {{-- Number --}}
                                <td class="px-6 py-4 font-semibold text-slate-500">

                                    {{ $users->firstItem() + $loop->index }}

                                </td>


                                {{-- Name --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 font-extrabold text-xs">

                                            @php
                                                $nameParts = preg_split('/\s+/', trim(strip_tags($user->name)));

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

                                        <span class="font-bold text-slate-800">
                                            {{ $user->name }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Email --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-2">

                                        @if ($user->hasVerifiedEmail())
                                            {{-- Email sudah terverifikasi --}}
                                            <span
                                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white"
                                                title="Email terverifikasi">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </span>
                                        @else
                                            {{-- Email belum terverifikasi --}}
                                            <span
                                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-rose-500 text-white"
                                                title="Email belum terverifikasi">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            </span>
                                        @endif

                                        <span class="text-slate-600">
                                            {{ $user->email }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Role --}}
                                <td class="px-6 py-4">

                                    <span
                                        class="inline-flex rounded-lg border px-2.5 py-1 text-xs font-extrabold uppercase {{ $roleClass }}">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>

                                </td>


                                {{-- Merchant --}}
                                <td class="px-6 py-4 text-slate-600">

                                    {{ $user->merchant?->name ?? '-' }}

                                </td>


                                {{-- Status --}}
                                <td class="px-6 py-4">

                                    @if ($user->status === 'active')
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Aktif

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-extrabold text-rose-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>

                                            Nonaktif

                                        </span>
                                    @endif

                                </td>


                                {{-- Action --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-center gap-2">


                                        {{-- Super Admin tidak bisa diedit/dihapus --}}
                                        @if ($user->role !== 'super_admin')
                                            {{-- Edit --}}
                                            <a href="{{ route('super_admin.accounts.edit', [
                                                'encryptedId' => $encryptedId,
                                            ]) }}"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600"
                                                title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>


                                            {{-- Delete --}}
                                            <form
                                                action="{{ route('super_admin.accounts.destroy', [
                                                    'encryptedId' => $encryptedId,
                                                ]) }}"
                                                method="POST" class="delete-account-form">

                                                @csrf

                                                @method('DELETE')

                                                <button type="submit"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                                    title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>

                                            </form>
                                        @else
                                            <span
                                                class="rounded-lg bg-purple-50 px-3 py-2 text-xs font-bold text-purple-600"
                                                title="Super Admin tidak dapat diubah">
                                                Protected
                                            </span>
                                        @endif

                                    </div>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-12 text-center">

                                    <div class="flex flex-col items-center justify-center">

                                        <div
                                            class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                            <i class="fa-solid fa-users text-lg"></i>
                                        </div>

                                        <p class="font-bold text-slate-700">
                                            Belum ada akun
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Belum terdapat akun pengguna yang tersedia.
                                        </p>

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">

                    {{ $users->links() }}

                </div>
            @endif


        </div>

    </div>
@endsection


@push('scripts')
    <script>
        document.querySelectorAll('.delete-account-form').forEach(form => {

            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({

                    title: 'Hapus akun?',

                    text: 'Akun yang dihapus tidak dapat digunakan lagi.',

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#ef4444',

                    cancelButtonColor: '#64748b',

                    confirmButtonText: 'Ya, Hapus',

                    cancelButtonText: 'Batal',

                    reverseButtons: true

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });

        });
    </script>
@endpush
