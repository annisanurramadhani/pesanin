<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MerchantKycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class KycIdentityController extends Controller
{
    /**
     * Menampilkan daftar pengajuan KYC identitas merchant.
     */
    public function index(Request $request)
    {
        $baseQuery = DB::table('merchant_kyc_submissions');

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)
                ->where('status', 'pending')
                ->count(),
            'approved' => (clone $baseQuery)
                ->where('status', 'approved')
                ->count(),
            'rejected' => (clone $baseQuery)
                ->where('status', 'rejected')
                ->count(),
        ];

        $query = DB::table('merchant_kyc_submissions')
            ->join(
                'merchants',
                'merchants.id',
                '=',
                'merchant_kyc_submissions.merchant_id'
            )
            ->select(
                'merchant_kyc_submissions.*',
                'merchants.name as merchant_name'
            )
            ->orderByRaw("
                CASE merchant_kyc_submissions.status
                    WHEN 'pending' THEN 1
                    WHEN 'rejected' THEN 2
                    WHEN 'approved' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc(
                'merchant_kyc_submissions.created_at'
            );

        if (in_array(
            $request->query('status'),
            ['pending', 'approved', 'rejected'],
            true
        )) {
            $query->where(
                'merchant_kyc_submissions.status',
                $request->query('status')
            );
        }

        $submissions = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'super_admin.kyc.identity.index',
            compact('submissions', 'stats')
        );
    }

    /**
     * Menyetujui pengajuan identitas merchant.
     */
    public function approve(int $id)
    {
        DB::transaction(function () use ($id) {
            $submission = MerchantKycSubmission::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            abort_if(
                !$submission,
                404,
                'Pengajuan identitas tidak ditemukan.'
            );

            if ($submission->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pengajuan yang sedang menunggu pemeriksaan yang dapat disetujui.',
                ]);
            }

            $submission->status = 'approved';
            $submission->rejection_note = null;
            $submission->reviewed_by = Auth::id();
            $submission->reviewed_at = now();

            $submission->save();
        });

        return redirect()
            ->route('super_admin.kyc_identity.index')
            ->with(
                'success',
                'Identitas merchant berhasil disetujui.'
            );
    }

    /**
     * Menolak pengajuan identitas merchant.
     */
    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'rejection_note' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        DB::transaction(function () use ($id, $validated) {
            $submission = MerchantKycSubmission::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            abort_if(
                !$submission,
                404,
                'Pengajuan identitas tidak ditemukan.'
            );

            if ($submission->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pengajuan yang sedang menunggu pemeriksaan yang dapat ditolak.',
                ]);
            }

            $submission->status = 'rejected';
            $submission->rejection_note =
                $validated['rejection_note'];
            $submission->reviewed_by = Auth::id();
            $submission->reviewed_at = now();

            $submission->save();
        });

        return redirect()
            ->route('super_admin.kyc_identity.index')
            ->with(
                'success',
                'Pengajuan identitas ditolak. Catatan penolakan telah disimpan.'
            );
    }

    /**
     * Menampilkan dokumen KYC dari S3 private.
     */
    public function document(int $id, string $type)
    {
        $columns = [
            'ktp' => 'ktp_path',
            'selfie' => 'selfie_path',
            'nib' => 'nib_path',
        ];

        abort_unless(isset($columns[$type]), 404);

        $submission = DB::table('merchant_kyc_submissions')
            ->where('id', $id)
            ->first();

        abort_if(
            !$submission,
            404,
            'Pengajuan identitas tidak ditemukan.'
        );

        $path = $submission->{$columns[$type]};

        abort_if(
            empty($path),
            404,
            'Dokumen tidak ditemukan.'
        );

        // Pastikan path hanya menunjuk ke direktori KYC.
        abort_if(
            str_contains($path, '..') ||
            !str_starts_with($path, 'kyc/'),
            404,
            'Path dokumen tidak valid.'
        );

        $extension = strtolower(
            pathinfo($path, PATHINFO_EXTENSION)
        );

        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
        ];

        abort_unless(
            isset($mimeTypes[$extension]),
            403,
            'Format dokumen tidak diizinkan.'
        );

        try {
            $contents = Storage::disk('s3_private')->get($path);
        } catch (\Throwable $exception) {
            report($exception);

            abort(
                502,
                'Dokumen gagal dibaca dari penyimpanan privat.'
            );
        }

        abort_if(
            $contents === '',
            404,
            'Dokumen kosong atau tidak ditemukan.'
        );

        return response($contents, 200, [
            'Content-Type' => $mimeTypes[$extension],
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Content-Disposition' => 'inline',
        ]);
    }
}
