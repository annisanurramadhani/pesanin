<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\MerchantKycSubmission;
use Illuminate\Support\Facades\Auth;

class MerchantKycSubmissionObserver
{
    public function updated(MerchantKycSubmission $submission): void
    {
        // Abaikan update yang tidak mengubah status.
        if (! $submission->wasChanged('status')) {
            return;
        }

        $oldStatus = $submission->getOriginal('status');
        $newStatus = $submission->status;

        // Audit hanya keputusan akhir verifikasi.
        if (! in_array($newStatus, ['approved', 'rejected'], true)) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $newStatus === 'approved'
                ? 'approve'
                : 'reject',
            'model_type' => MerchantKycSubmission::class,
            'model_id' => $submission->id,

            'old_values' => [
                'status' => $oldStatus,
            ],

            'new_values' => [
                'status' => $newStatus,
                'reviewed_by' => $submission->reviewed_by,
                'reviewed_at' => $submission->reviewed_at?->toDateTimeString(),
                'rejection_note' => $newStatus === 'rejected'
                    ? $submission->rejection_note
                    : null,
            ],
        ]);
    }
}
