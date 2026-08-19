<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required',],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->validateRecaptcha();
        /*
        |--------------------------------------------------------------------------
        | Cari User
        |--------------------------------------------------------------------------
        */

        $email = strtolower(trim($this->input('email')));

        $user = User::where('email', $email)->first();


        /*
        |--------------------------------------------------------------------------
        | User Tidak Ditemukan
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Cek Status Akun
        |--------------------------------------------------------------------------
        */

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' =>
                    'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Cek Akun Sedang Dikunci
        |--------------------------------------------------------------------------
        */

        if (
            $user->login_locked_until &&
            now()->lt($user->login_locked_until)
        ) {
            $seconds = now()->diffInSeconds(
                $user->login_locked_until
            );

            $minutes = max(
                1,
                (int) ceil($seconds / 60)
            );

            throw ValidationException::withMessages([
                'email' =>
                    "Akun Anda sedang dikunci. Silakan coba lagi dalam {$minutes} menit.",
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Cek Apakah Lock Sudah Berakhir
        |--------------------------------------------------------------------------
        */

        $lockExpired =
            $user->login_locked_until &&
            now()->gte($user->login_locked_until);


        /*
        |--------------------------------------------------------------------------
        | Cek Password
        |--------------------------------------------------------------------------
        */

        if (!Hash::check(
            $this->input('password'),
            $user->password
        )) {

            /*
            |--------------------------------------------------------------------------
            | Lock Sudah Berakhir + Password Masih Salah
            |--------------------------------------------------------------------------
            */

            if ($lockExpired) {

                $user->update([
                    'status' => 'inactive',
                    'failed_login_attempts' => 0,
                    'login_locked_until' => null,
                ]);

                throw ValidationException::withMessages([
                    'email' =>
                        'Password masih salah setelah masa penguncian berakhir. Akun Anda telah dinonaktifkan.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Tambah Failed Attempt
            |--------------------------------------------------------------------------
            */

            $failedAttempts =
                (int) $user->failed_login_attempts + 1;


            /*
            |--------------------------------------------------------------------------
            | Percobaan Ke-3 → Lock 30 Menit
            |--------------------------------------------------------------------------
            */

            if ($failedAttempts >= 3) {

                $user->update([
                    'failed_login_attempts' => 3,
                    'login_locked_until' => now()->addMinutes(30),
                ]);

                throw ValidationException::withMessages([
                    'email' =>
                        'Password salah 3 kali. Akun Anda dikunci selama 30 menit.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan Failed Attempt
            |--------------------------------------------------------------------------
            */

            $user->update([
                'failed_login_attempts' => $failedAttempts,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Sisa Percobaan
            |--------------------------------------------------------------------------
            */

            $remainingAttempts = 3 - $failedAttempts;

            throw ValidationException::withMessages([
                'email' =>
                    "Email atau password salah. Sisa percobaan: {$remainingAttempts} kali.",
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Password Benar
        |--------------------------------------------------------------------------
        */

        $user->update([
            'failed_login_attempts' => 0,
            'login_locked_until' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user,
            $this->boolean('remember')
        );
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
    protected function validateRecaptcha(): void
    {
        $response = $this->input('g-recaptcha-response');

        if (!$response) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' =>
                    'Silakan centang "I\'m not a robot".',
            ]);
        }

        try {
            $result = Http::asForm()
                ->timeout(10)
                ->post(
                    'https://www.google.com/recaptcha/api/siteverify',
                    [
                        'secret' => config(
                            'services.recaptcha.secret_key'
                        ),

                        'response' => $response,

                        'remoteip' => $this->ip(),
                    ]
                )
                ->json();

        } catch (\Throwable $e) {

            report($e);

            throw ValidationException::withMessages([
                'g-recaptcha-response' =>
                    'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ]);
        }

        if (!($result['success'] ?? false)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' =>
                    'Verifikasi reCAPTCHA tidak valid. Silakan coba lagi.',
            ]);
        }
    }
}
