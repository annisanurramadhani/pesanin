<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MerchantKycController extends Controller
{
    public function index(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        abort_if(!$merchantId, 403, 'Akun tidak terhubung dengan merchant.');

        $submission = DB::table('merchant_kyc_submissions')
            ->where('merchant_id', $merchantId)
            ->first();

        $bankAccount = DB::table('merchant_bank_accounts')
            ->where('merchant_id', $merchantId)
            ->first();

        $kycStatus = $submission?->status ?? 'not_submitted';

        $bankStatus = $bankAccount?->status ?? 'locked';

        if ($bankAccount && $bankAccount->is_locked && $bankStatus === 'active') {
            $bankStatus = 'approved';
        }

        $currentStep = $kycStatus === 'approved' ? 2 : 1;

        $kycRejectionNote = $submission?->rejection_note;

        $bankRejectionNote = $bankAccount->rejection_note ?? null;

        return view('merchant.kyc.index', compact(
            'submission',
            'bankAccount',
            'kycStatus',
            'bankStatus',
            'currentStep',
            'kycRejectionNote',
            'bankRejectionNote'
        ));
    }

    public function store(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        abort_if(!$merchantId, 403, 'Akun tidak terhubung dengan merchant.');

        $submission = DB::table('merchant_kyc_submissions')
            ->where('merchant_id', $merchantId)
            ->first();

        if ($submission && in_array($submission->status, ['pending', 'approved'], true)) {
            return back()->with(
                'error',
                $submission->status === 'approved'
                    ? 'Identitas sudah disetujui dan tidak dapat diubah.'
                    : 'Pengajuan identitas sedang diperiksa admin.'
            );
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('merchant_kyc_submissions', 'nik')
                    ->ignore($submission?->id),
            ],
            'ktp_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'selfie_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'nib_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $ktpPath = null;
        $selfiePath = null;
        $nibPath = null;

        try {
            $ktpPath = $request->file('ktp_file')->store(
                "kyc/{$merchantId}/ktp",
                'local'
            );

            $selfiePath = $request->file('selfie_file')->store(
                "kyc/{$merchantId}/selfie",
                'local'
            );

            if ($request->hasFile('nib_file')) {
                $nibPath = $request->file('nib_file')->store(
                    "kyc/{$merchantId}/nib",
                    'local'
                );
            }

            $data = [
                'full_name' => $validated['full_name'],
                'nik' => $validated['nik'],
                'ktp_path' => $ktpPath,
                'selfie_path' => $selfiePath,
                'business_name' => $validated['business_name'] ?? null,
                'nib_path' => $nibPath,
                'status' => 'pending',
                'rejection_note' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'updated_at' => now(),
            ];

            if ($submission) {
                DB::table('merchant_kyc_submissions')
                    ->where('id', $submission->id)
                    ->update($data);

                foreach ([
                    $submission->ktp_path,
                    $submission->selfie_path,
                    $submission->nib_path,
                ] as $oldPath) {
                    if ($oldPath) {
                        Storage::disk('local')->delete($oldPath);
                    }
                }
            } else {
                $data['merchant_id'] = $merchantId;
                $data['created_at'] = now();

                DB::table('merchant_kyc_submissions')->insert($data);
            }
        } catch (\Throwable $exception) {
            foreach ([$ktpPath, $selfiePath, $nibPath] as $path) {
                if ($path) {
                    Storage::disk('local')->delete($path);
                }
            }

            throw $exception;
        }

        return redirect()
            ->route('merchant.kyc.index')
            ->with('success', 'Pengajuan identitas berhasil dikirim dan menunggu pemeriksaan admin.');
    }

    public function storeBank(Request $request)
    {
        $merchantId = $request->user()->merchant_id;

        abort_if(!$merchantId, 403, 'Akun tidak terhubung dengan merchant.');

        $submission = DB::table('merchant_kyc_submissions')
            ->where('merchant_id', $merchantId)
            ->first();

        if (!$submission || $submission->status !== 'approved') {
            return back()->with(
                'error',
                'Lengkapi verifikasi identitas dan tunggu persetujuan admin terlebih dahulu.'
            );
        }

        $existingAccount = DB::table('merchant_bank_accounts')
            ->where('merchant_id', $merchantId)
            ->first();

        if ($existingAccount) {
            if ($existingAccount->is_locked || $existingAccount->status === 'active') {
                return back()->with(
                    'error',
                    'Rekening sudah tersimpan atau terkunci. Hubungi admin jika perlu perubahan.'
                );
            }

            if ($existingAccount->status === 'pending') {
                return back()->with(
                    'error',
                    'Pengajuan rekening sedang diperiksa admin.'
                );
            }

            if ($existingAccount->status !== 'rejected') {
                return back()->with(
                    'error',
                    'Rekening yang sudah terdaftar tidak dapat diganti melalui halaman ini.'
                );
            }
        }

        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:100'],
            'bank_name' => [
                'required',
                Rule::in(['BCA', 'BRI', 'BNI', 'MANDIRI']),
            ],
                        
            'account_number' => [
                'required',
                'string',
                'regex:/^[0-9]{1,20}$/',
            ],

        ]);

        if ($existingAccount) {
            DB::table('merchant_bank_accounts')
                ->where('id', $existingAccount->id)
                ->update([
                    'bank_name' => $validated['bank_name'],
                    'account_number' => $validated['account_number'],
                    'account_name' => $validated['account_name'],
                    'status' => 'pending',
                    'is_locked' => true,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('merchant_bank_accounts')->insert([
                'merchant_id' => $merchantId,
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'status' => 'pending',
                'is_locked' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('merchant.kyc.index')
            ->with('success', 'Pengajuan rekening berhasil dikirim dan menunggu pemeriksaan admin.');
    }
}
