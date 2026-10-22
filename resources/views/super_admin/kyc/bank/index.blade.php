
@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-2 flex items-center gap-2 text-sm text-slate-500">
                <span>Super Admin</span>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span>Verifikasi KYC</span>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="font-semibold text-amber-600">Verifikasi Rekening</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                Verifikasi Rekening
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Periksa informasi rekening merchant sebelum rekening digunakan untuk penarikan saldo.
            </p>
        </div>

        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
            <i class="fa-solid fa-building-columns text-xl"></i>
        </div>
    </div>

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
            <p class="mb-2 font-semibold">Terjadi kesalahan:</p>
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
            <p class="mt-1 text-xs text-slate-500">Seluruh rekening merchant</p>
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
            <p class="mt-1 text-xs text-slate-500">Rekening berhasil diverifikasi</p>
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
            <p class="mt-1 text-xs text-slate-500">Perlu diajukan kembali</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="font-bold text-slate-800">Daftar Rekening Merchant</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Periksa nama pemilik, nama bank, dan nomor rekening.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <form
                    action="{{ route('super_admin.kyc_bank.index') }}"
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
                        <option value="active" @selected(request('status') === 'active')>
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
                    href="{{ route('super_admin.kyc_bank.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                    Reset Filter
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Merchant</th>
                        <th class="px-5 py-4 font-semibold">Pemilik Rekening</th>
                        <th class="px-5 py-4 font-semibold">Informasi Bank</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Tanggal Pengajuan</th>
                        <th class="px-5 py-4 text-right font-semibold">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($accounts as $account)
                        <tr class="align-top transition hover:bg-slate-50/70">
                            <td class="px-5 py-5">
                                <p class="font-bold text-slate-800">
                                    {{ $account->merchant_name }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Merchant ID: {{ $account->merchant_id }}
                                </p>
                            </td>

                            <td class="px-5 py-5">
                                <p class="font-semibold text-slate-800">
                                    {{ $account->account_name }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Nomor rekening
                                </p>
                                <p class="mt-1 font-mono text-sm text-slate-700">
                                    {{ $account->account_number }}
                                </p>
                            </td>

                            <td class="px-5 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">
                                            {{ $account->bank_name }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            Rekening merchant
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-5">
                                @if ($account->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @elseif ($account->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1.5 text-xs font-bold text-green-700">
                                        <i class="fa-solid fa-check"></i>
                                        Disetujui
                                    </span>
                                @elseif ($account->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1.5 text-xs font-bold text-red-700">
                                        <i class="fa-solid fa-xmark"></i>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                        {{ ucfirst($account->status) }}
                                    </span>
                                @endif

                                @if ($account->is_locked)
                                    <p class="mt-2 text-xs text-slate-500">
                                        <i class="fa-solid fa-lock mr-1"></i>
                                        Data terkunci
                                    </p>
                                @endif
                            </td>

                            <td class="px-5 py-5 text-slate-600">
                                <p>{{ \Illuminate\Support\Carbon::parse($account->created_at)->format('d M Y') }}</p>
                                <p class="mt-1 text-xs text-slate-400">
                                    {{ \Illuminate\Support\Carbon::parse($account->created_at)->format('H:i') }}
                                </p>
                            </td>

                            <td class="px-5 py-5">
                                @if ($account->status === 'pending')
                                    <div class="flex min-w-[145px] flex-col items-stretch gap-2">
                                        <form
                                            action="{{ route('super_admin.kyc_bank.approve', $account->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Setujui rekening merchant ini? Pastikan data rekening telah diperiksa.')"
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

                                        <form
                                            action="{{ route('super_admin.kyc_bank.reject', $account->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Tolak pengajuan rekening ini? Merchant perlu mengajukan kembali.')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2.5 text-xs font-bold text-red-600 transition hover:bg-red-50"
                                            >
                                                <i class="fa-solid fa-xmark"></i>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
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
                                    <i class="fa-solid fa-building-columns text-xl"></i>
                                </div>
                                <p class="mt-4 font-semibold text-slate-700">
                                    Belum ada rekening merchant
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Pengajuan rekening yang masuk akan muncul di halaman ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($accounts->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
