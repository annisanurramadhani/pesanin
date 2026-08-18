<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    /**
     * Menampilkan halaman input kode OTP.
     */
    public function show()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Kalau sudah terverifikasi
        |--------------------------------------------------------------------------
        */

        if ($user->email_verified_at) {
            return redirect()->route('merchant.setup');
        }

        return view('auth.verify-code');
    }

    /**
     * Memproses kode OTP.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => [
                'required',
                'digits:6',
            ],
        ]);

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Sudah Terverifikasi
        |--------------------------------------------------------------------------
        */

        if ($user->email_verified_at) {
            return redirect()->route('merchant.setup');
        }

        /*
        |--------------------------------------------------------------------------
        | Cek Kode
        |--------------------------------------------------------------------------
        */

        if (!$user->verification_code) {
            return back()->withErrors([
                'code' => 'Kode verifikasi tidak ditemukan.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cek Expired
        |--------------------------------------------------------------------------
        */

        if (
            !$user->verification_code_expires_at ||
            now()->greaterThan($user->verification_code_expires_at)
        ) {
            return back()->withErrors([
                'code' => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim ulang kode.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Cek OTP
        |--------------------------------------------------------------------------
        */

        if ($user->verification_code !== $request->code) {
            return back()->withErrors([
                'code' => 'Kode verifikasi salah.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Verifikasi Berhasil
        |--------------------------------------------------------------------------
        */

        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Refresh Data User
        |--------------------------------------------------------------------------
        */

        $user->refresh();

        /*
        |--------------------------------------------------------------------------
        | Pastikan Berhasil
        |--------------------------------------------------------------------------
        */

        if (!$user->email_verified_at) {
            return back()->withErrors([
                'code' => 'Email gagal diverifikasi. Data tidak berhasil disimpan.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Lanjut Setup Merchant
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('merchant.setup')
            ->with(
                'success',
                'Email berhasil diverifikasi. Silakan lengkapi data toko.'
            );
    }

    /**
     * Kirim ulang OTP.
     */
    public function resend()
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect()->route('merchant.setup');
        }

        $verificationCode = (string) random_int(100000, 999999);

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)
            ->send(new VerificationCodeMail($verificationCode));

        return back()->with(
            'success',
            'Kode verifikasi baru telah dikirim ke email Anda.'
        );
    }
}
