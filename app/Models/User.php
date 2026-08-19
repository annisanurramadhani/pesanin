<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Atribut yang diizinkan untuk mass-assignment (Create/Update).
     */
    protected $fillable = [
        'merchant_id',
        'name',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
        'failed_login_attempts',
        'login_locked_until',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
        'login_locked_until' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi JSON/Array.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke model Merchant.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }
}
