@extends('layouts.merchant')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Verifikasi Identitas (KYC)
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            Kelola dan lengkapi informasi verifikasi identitas merchant Anda.
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                <i class="fa-solid fa-shield-halved text-xl"></i>
            </div>

            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Halaman KYC Merchant
                </h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Halaman ini disiapkan untuk proses verifikasi identitas
                    merchant. Formulir, unggah dokumen, dan status verifikasi
                    akan ditambahkan setelah alur KYC ditentukan.
                </p>

                <span class="mt-4 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    Belum dikonfigurasi
                </span>
            </div>
        </div>
    </div>
</div>
@endsection