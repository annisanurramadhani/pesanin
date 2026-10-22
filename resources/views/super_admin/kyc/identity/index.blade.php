
@extends('layouts.admin')

        @section('header')
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Verifikasi Identitas
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Periksa identitas merchant sebelum mengizinkan pengajuan rekening bank.
                    </p>
                </div>
            </div>
        @endsection 

@section('content')
<div class="mx-auto max-w-7xl space-y-6">


    @if (session('success'))
        <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="mb-2 font-semibold">Periksa kembali data berikut:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-slate-500">Total Pengajuan</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                    <i class="fa-solid fa-folder-open"></i>
                </span>
            </div>
            <p class="mt-4 text-3xl font-bold text-slate-800">
                {{ $stats['total'] }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Seluruh pengajuan identitas</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-white p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-slate-500">Menunggu Pemeriksaan</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <p class="mt-4 text-3xl font-bold text-slate-800">
                {{ $stats['pending'] }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Menunggu tindakan admin</p>
        </div>

        <div class="rounded-2xl border border-green-200 bg-white p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-slate-500">Disetujui</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <p class="mt-4 text-3xl font-bold text-slate-800">
                {{ $stats['approved'] }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Identitas berhasil diverifikasi</p>
        </div>

        <div class="rounded-2xl border border-red-200 bg-white p-5">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-medium text-slate-500">Ditolak</p>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
            </div>
            <p class="mt-4 text-3xl font-bold text-slate-800">
                {{ $stats['rejected'] }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Perlu diperbaiki merchant</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="font-bold text-slate-800">Daftar Pengajuan Identitas</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Periksa kecocokan data dan dokumen yang dikirim merchant.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <form
                    action="{{ route('super_admin.kyc_identity.index') }}"
                    method="GET"
                    class="flex flex-col gap-2 sm:flex-row sm:items-center"
                >
                    <select
                        name="status"
                        class="min-w-52 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>
                            Menunggu Pemeriksaan
                        </option>
                        <option value="approved" @selected(request('status') === 'approved')>
                            Disetujui
                        </option>
                        <option value="rejected" @selected(request('status') === 'rejected')>
                            Ditolak
                        </option>
                    </select>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-amber-400"
                    >
                        <i class="fa-solid fa-filter"></i>
                        Filter
                    </button>
                </form>

                <a
                    href="{{ route('super_admin.kyc_identity.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                    Reset Filter
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Merchant</th>
                        <th class="px-5 py-4 font-semibold">Identitas</th>
                        <th class="px-5 py-4 font-semibold">Dokumen</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Tanggal Pengajuan</th>
                        <th class="px-5 py-4 text-right font-semibold">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $submission)
                        <tr class="align-top transition hover:bg-slate-50/70">
                            <td class="px-5 py-5">
                                <p class="font-bold text-slate-800">
                                    {{ $submission->merchant_name }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Merchant ID: {{ $submission->merchant_id }}
                                </p>

                                @if ($submission->business_name)
                                    <p class="mt-2 text-xs text-slate-600">
                                        Usaha: {{ $submission->business_name }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-5 py-5">
                                <p class="font-semibold text-slate-800">
                                    {{ $submission->full_name }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    NIK: {{ $submission->nik }}
                                </p>
                            </td>

                            <td class="px-5 py-5">
                                <div class="flex flex-col items-start gap-2">
                                    <a
                                        href="{{ route('super_admin.kyc_identity.document', [$submission->id, 'ktp']) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 font-semibold text-amber-600 hover:text-amber-700"
                                    >
                                        <i class="fa-solid fa-file-image"></i>
                                        Lihat KTP
                                    </a>

                                    <a
                                        href="{{ route('super_admin.kyc_identity.document', [$submission->id, 'selfie']) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 font-semibold text-amber-600 hover:text-amber-700"
                                    >
                                        <i class="fa-solid fa-camera"></i>
                                        Lihat Selfie
                                    </a>

                                    @if ($submission->nib_path)
                                        <a
                                            href="{{ route('super_admin.kyc_identity.document', [$submission->id, 'nib']) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex items-center gap-2 font-semibold text-slate-600 hover:text-slate-800"
                                        >
                                            <i class="fa-solid fa-file-lines"></i>
                                            Lihat NIB
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-5">
                                @if ($submission->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @elseif ($submission->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1.5 text-xs font-bold text-green-700">
                                        <i class="fa-solid fa-check"></i>
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700">
                                        <i class="fa-solid fa-xmark"></i>
                                        Ditolak
                                    </span>
                                @endif

                                @if ($submission->rejection_note)
                                    <p class="mt-2 max-w-[200px] text-xs leading-5 text-red-600">
                                        {{ $submission->rejection_note }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-5 py-5 text-slate-600">
                                <p>{{ \Illuminate\Support\Carbon::parse($submission->created_at)->format('d M Y') }}</p>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ \Illuminate\Support\Carbon::parse($submission->created_at)->format('H:i') }}
                                </p>
                            </td>

                            <td class="px-5 py-5">
                                @if ($submission->status === 'pending')
                                    <div class="flex min-w-[150px] flex-col items-stretch gap-2">
                                        
                                        <form
                                            action="{{ route('super_admin.kyc_identity.approve', $submission->id) }}"
                                            method="POST"
                                            class="approve-identity-form"
                                            data-merchant="{{ $submission->merchant_name }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-3 py-2.5 text-xs font-bold text-white transition hover:bg-green-700"
                                            >
                                                <i class="fa-solid fa-check"></i>
                                                Setujui
                                            </button>
                                        </form>


                                        <button
                                            type="button"
                                            onclick="document.getElementById('reject-identity-{{ $submission->id }}').showModal()"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50"
                                        >
                                            <i class="fa-solid fa-xmark"></i>
                                            Tolak
                                        </button>
                                    </div>

                                    <dialog
                                        id="reject-identity-{{ $submission->id }}"
                                        class="w-[calc(100%-2rem)] max-w-lg rounded-2xl p-0 shadow-2xl backdrop:bg-slate-950/50"
                                    >
                                        <form
                                            action="{{ route('super_admin.kyc_identity.reject', $submission->id) }}"
                                            method="POST"
                                            class="p-6"
                                        >
                                            @csrf

                                            <div class="mb-5 flex items-start gap-3">
                                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                                                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-800">Tolak Verifikasi Identitas</h3>
                                                    <p class="mt-1 text-sm text-slate-500">
                                                        Masukkan alasan penolakan agar merchant dapat memperbaiki pengajuannya.
                                                    </p>
                                                </div>
                                            </div>

                                            <label
                                                for="rejection_note_{{ $submission->id }}"
                                                class="mb-2 block text-sm font-semibold text-slate-700"
                                            >
                                                Alasan penolakan
                                            </label>

                                            <textarea
                                                id="rejection_note_{{ $submission->id }}"
                                                name="rejection_note"
                                                rows="4"
                                                maxlength="2000"
                                                required
                                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                                                placeholder="Contoh: Foto KTP tidak jelas, silakan unggah ulang."
                                            ></textarea>

                                            <div class="mt-5 flex justify-end gap-3">
                                                <button
                                                    type="button"
                                                    onclick="this.closest('dialog').close()"
                                                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                                                >
                                                    Batal
                                                </button>

                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700"
                                                >
                                                    Tolak Pengajuan
                                                </button>
                                            </div>
                                        </form>
                                    </dialog>
                                @else
                                    <span class="text-xs text-slate-400">
                                        Sudah diperiksa
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <i class="fa-solid fa-folder-open text-xl"></i>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">
                                    Belum ada pengajuan identitas
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Pengajuan dari merchant akan muncul di halaman ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($submissions->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/super_admin/kyc-identity.js') }}"></script>
@endpush
@endsection
