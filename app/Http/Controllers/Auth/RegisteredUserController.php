<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman register.
     */
    public function create(Request $request): View
    {
        return view('auth.register', [
            'encryptedDuration' => $request->query('duration'),
        ]);
    }

    /**
     * Memproses registrasi user.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Za-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],

            'duration' => [
                'required',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi Duration
        |--------------------------------------------------------------------------
        */

        $durationId = decryptId($request->duration);

        if (!$durationId) {
            return redirect()
                ->route('public.subscription.index')
                ->with('error', 'Durasi langganan tidak valid.');
        }

        /*
        |--------------------------------------------------------------------------
        | Buat User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Simpan Duration ke Session
        |--------------------------------------------------------------------------
        */

        session([
            'subscription.duration_id' => $durationId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate OTP
        |--------------------------------------------------------------------------
        */

        $verificationCode = (string) random_int(100000, 999999);

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Login User
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        /*
        |--------------------------------------------------------------------------
        | Kirim OTP ke Email
        |--------------------------------------------------------------------------
        */

        Mail::to($user->email)
            ->send(new VerificationCodeMail($verificationCode));

        /*
        |--------------------------------------------------------------------------
        | Masuk ke Halaman OTP
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('verification.code')
            ->with(
                'success',
                'Kode verifikasi telah dikirim ke email Anda.'
            );
    }
}
