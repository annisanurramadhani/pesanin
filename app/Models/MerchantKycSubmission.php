<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchantKycSubmission extends Model
{
    protected $table = 'merchant_kyc_submissions';

    protected $fillable = [
        'merchant_id',
        'full_name',
        'nik',
        'ktp_path',
        'selfie_path',
        'business_name',
        'nib_path',
        'status',
        'rejection_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
