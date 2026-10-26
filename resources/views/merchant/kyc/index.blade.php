@extends('layouts.merchant')

@section('header')
    <div>
        <h1 class="text-2xl font-black text-slate-800">
            Verifikasi Identitas (KYC)
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Lengkapi identitas dan rekening bank untuk menggunakan layanan penarikan saldo.
        </p>
    </div>
@endsection

@section('content')
    @php
        $kycStatus = $kycStatus ?? 'not_submitted';
        $bankStatus = $bankStatus ?? 'locked';
        $currentStep = $currentStep ?? 1;
        $kycRejectionNote = $kycRejectionNote ?? null;
        $bankRejectionNote = $bankRejectionNote ?? null;
        $identityLocked = $kycStatus === 'approved';
        $bankLocked = $bankStatus === 'approved';
        $bankStepAvailable = $identityLocked;
    @endphp

    <div class="max-w-7xl mx-auto">

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div
                class="rounded-xl border p-4 {{ $currentStep === 1 ? 'border-amber-400 bg-amber-50' : 'border-slate-200 bg-white' }}">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Step 1
                </p>
                <h2 class="mt-1 font-bold text-slate-800">Verifikasi Identitas</h2>
                <p class="mt-2 text-sm text-slate-600">
                    Status:
                    @switch($kycStatus)
                        @case('pending')
                            Menunggu persetujuan admin
                        @break

                        @case('approved')
                            Disetujui
                        @break

                        @case('rejected')
                            Perlu diperbaiki
                        @break

                        @default
                            Belum dikirim
                    @endswitch
                </p>
            </div>

            <div
                class="rounded-xl border p-4 {{ $currentStep === 2 ? 'border-amber-400 bg-amber-50' : 'border-slate-200 bg-white' }}">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Step 2
                </p>
                <h2 class="mt-1 font-bold text-slate-800">Rekening Bank</h2>
                <p class="mt-2 text-sm text-slate-600">
                    @if (!$bankStepAvailable)
                        Tersedia setelah identitas disetujui
                    @elseif ($bankStatus === 'pending')
                        Menunggu persetujuan admin
                    @elseif ($bankStatus === 'approved')
                        Disetujui dan terkunci
                    @elseif ($bankStatus === 'rejected')
                        Perlu diperbaiki
                    @else
                        Siap dilengkapi
                    @endif
                </p>
            </div>
        </div>

        @if (!$identityLocked)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            Step 1 — Verifikasi Identitas
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Isi data sesuai dokumen resmi. Step 2 akan terbuka setelah admin menyetujui identitas.
                        </p>
                    </div>
                </div>

                @if ($kycStatus === 'pending')
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        Pengajuan identitas sedang diperiksa admin. Data tidak dapat diubah selama proses pemeriksaan.
                    </div>
                @else
                    @if ($kycStatus === 'rejected' && $kycRejectionNote)
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <strong>Catatan admin:</strong> {{ $kycRejectionNote }}
                        </div>
                    @endif

                    <form action="{{ route('merchant.kyc.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-5">
                        @csrf

                        <div>
                            <label for="full_name" class="mb-2 block text-sm font-semibold text-slate-700">
                                Nama lengkap sesuai KTP
                            </label>
                            <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}"
                                maxlength="255" autocomplete="name" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100"
                                placeholder="Masukkan nama sesuai KTP">
                        </div>

                        <div>
                            <label for="nik" class="mb-2 block text-sm font-semibold text-slate-700">
                                NIK (16 digit)
                            </label>
                            <input id="nik" name="nik" type="text" inputmode="numeric" pattern="[0-9]{16}"
                                minlength="16" maxlength="16" autocomplete="off" value="{{ old('nik') }}" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100"
                                placeholder="Masukkan 16 digit NIK">
                        </div>

                        <div>
                            <label for="ktp_file" class="mb-2 block text-sm font-semibold text-slate-700">
                                Foto KTP / PDF
                            </label>
                            <input id="ktp_file" name="ktp_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                required
                                class="block w-full rounded-xl border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700">
                            <p class="mt-1 text-xs text-slate-500">
                                JPG, PNG, WEBP, atau PDF. Maksimal 5 MB.
                            </p>
                        </div>

                        <div>
                            <label for="selfie_file" class="mb-2 block text-sm font-semibold text-slate-700">
                                Selfie memegang KTP
                            </label>
                            <input id="selfie_file" name="selfie_file" type="file" accept=".jpg,.jpeg,.png,.webp"
                                required
                                class="block w-full rounded-xl border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700">
                            <p class="mt-1 text-xs text-slate-500">
                                JPG, PNG, atau WEBP. Maksimal 5 MB.
                            </p>
                        </div>

                        <div>
                            <label for="business_name" class="mb-2 block text-sm font-semibold text-slate-700">
                                Nama badan usaha (opsional)
                            </label>
                            <input id="business_name" name="business_name" type="text" maxlength="255"
                                value="{{ old('business_name') }}"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100"
                                placeholder="Masukkan nama badan usaha">
                        </div>

                        <div>
                            <label for="nib_file" class="mb-2 block text-sm font-semibold text-slate-700">
                                NIB / dokumen usaha (opsional)
                            </label>
                            <input id="nib_file" name="nib_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                                class="block w-full rounded-xl border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700">
                            <p class="mt-1 text-xs text-slate-500">
                                JPG, PNG, WEBP, atau PDF. Maksimal 5 MB.
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-400">
                            Kirim Identitas untuk Verifikasi
                        </button>
                    </form>
                @endif
            </div>
        @elseif (!$bankLocked)
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="fa-solid fa-building-columns text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            Step 2 — Rekening Bank
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Masukkan rekening atas nama yang sesuai dan periksa kembali sebelum mengirim.
                        </p>
                    </div>
                </div>

                @if ($bankStatus === 'pending')
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        Rekening bank sedang diperiksa admin. Data tidak dapat diubah selama proses pemeriksaan.
                    </div>
                @else
                    @if ($bankStatus === 'rejected' && $bankRejectionNote)
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <strong>Catatan admin:</strong> {{ $bankRejectionNote }}
                        </div>
                    @endif

                    <form action="{{ route('merchant.kyc.bank.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="account_name" class="mb-2 block text-sm font-semibold text-slate-700">
                                Nama pemilik rekening
                            </label>
                            <input id="account_name" name="account_name" type="text"
                                value="{{ old('account_name') }}" maxlength="255" required autocomplete="off"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100"
                                placeholder="Nama sesuai rekening bank">
                        </div>

                        <div>
                            <label for="bank_name" class="mb-2 block text-sm font-semibold text-slate-700">
                                Nama bank
                            </label>
                            <select id="bank_name" name="bank_name" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100">
                                <option value="">Pilih bank</option>
                                <option value="BCA" @selected(old('bank_name') === 'BCA')>BCA</option>
                                <option value="BRI" @selected(old('bank_name') === 'BRI')>BRI</option>
                                <option value="BNI" @selected(old('bank_name') === 'BNI')>BNI</option>
                                <option value="MANDIRI" @selected(old('bank_name') === 'MANDIRI')>Bank Mandiri</option>
                            </select>
                        </div>



                        <input id="account_number" name="account_number" type="text" inputmode="numeric"
                            pattern="[0-9]{1,20}" maxlength="20" value="{{ old('account_number') }}" required
                            autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-100"
                            placeholder="Masukkan nomor rekening (maksimal 20 digit)">



                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                            Periksa kembali nama bank dan nomor rekening. Setelah disetujui admin, data ini akan dikunci.
                            Perubahan berikutnya harus diajukan melalui admin.
                        </div>

                        <button type="submit"
                            class="w-full rounded-xl bg-amber-500 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-amber-400">
                            Kirim Rekening untuk Verifikasi
                        </button>
                    </form>
                @endif
            </div>
        @else
            <div class="rounded-2xl border border-green-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                        <i class="fa-solid fa-lock text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">
                            KYC Telah Disetujui
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Identitas dan rekening bank telah disetujui admin. Data tidak dapat diubah melalui halaman
                            merchant.
                            Untuk mengubah data, silakan hubungi admin.
                        </p>
                        <span
                            class="mt-4 inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            Verifikasi selesai
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
