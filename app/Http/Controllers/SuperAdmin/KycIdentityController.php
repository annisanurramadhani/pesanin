<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\MerchantKycSubmission;
use Illuminate\Support\Facades\Auth;

class KycIdentityController extends Controller
{

    public function index(Request $request)
    {
        $baseQuery = DB::table('merchant_kyc_submissions');

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $query = DB::table('merchant_kyc_submissions')
            ->join('merchants', 'merchants.id', '=', 'merchant_kyc_submissions.merchant_id')
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
            ->orderByDesc('merchant_kyc_submissions.created_at');

        if (in_array($request->query('status'), ['pending', 'approved', 'rejected'], true)) {
            $query->where('merchant_kyc_submissions.status', $request->query('status'));
        }

        $submissions = $query->paginate(10)->withQueryString();

        return view('super_admin.kyc.identity.index', compact('submissions', 'stats'));
    }


    public function approve(int $id)
    {
        DB::transaction(function () use ($id) {
            $submission = MerchantKycSubmission::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            abort_if(!$submission, 404, 'Pengajuan identitas tidak ditemukan.');

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
            ->with('success', 'Identitas merchant berhasil disetujui.');
    }

    public function reject(Request $request, int $id)
    {
        $validated = $request->validate([
            'rejection_note' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($id, $validated) {
            $submission = MerchantKycSubmission::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->first();

            abort_if(!$submission, 404, 'Pengajuan identitas tidak ditemukan.');

            if ($submission->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pengajuan yang sedang menunggu pemeriksaan yang dapat ditolak.',
                ]);
            }

            $submission->status = 'rejected';
            $submission->rejection_note = $validated['rejection_note'];
            $submission->reviewed_by = \Illuminate\Support\Facades\Auth::id();
            $submission->reviewed_at = now();

            $submission->save();
        });

        return redirect()
            ->route('super_admin.kyc_identity.index')
            ->with('success', 'Pengajuan identitas ditolak. Catatan penolakan telah disimpan.');
    }

    public function document(int $id, string $type)
    {
        $columns = [
            'ktp' => 'ktp_path',
            'selfie' => 'selfie_path',
            'nib' => 'nib_path',
        ];

        abort_unless(array_key_exists($type, $columns), 404);

        $submission = DB::table('merchant_kyc_submissions')
            ->where('id', $id)
            ->first();

        abort_if(!$submission, 404, 'Pengajuan identitas tidak ditemukan.');

        $path = $submission->{$columns[$type]};

        abort_if(!$path || !Storage::disk('local')->exists($path), 404, 'Dokumen tidak ditemukan.');

        $absolutePath = Storage::disk('local')->path($path);
        $mimeType = mime_content_type($absolutePath) ?: 'application/octet-stream';

        abort_unless(
            in_array($mimeType, [
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/pdf',
            ], true),
            403
        );

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Content-Disposition' => 'inline',
        ]);
    }
}
