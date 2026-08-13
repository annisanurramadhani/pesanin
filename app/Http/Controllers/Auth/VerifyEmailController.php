<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Verifikasi email melalui signed URL.
     */
    public function __invoke(
        Request $request,
        string $id,
        string $hash
    ): RedirectResponse {

        // Pastikan URL verifikasi masih valid
        if (! $request->hasValidSignature()) {
            abort(403, 'Link verifikasi tidak valid atau sudah kedaluwarsa.');
        }

        // Cari akun berdasarkan ID
        $user = User::findOrFail($id);

        // Pastikan hash email sesuai
        if (! hash_equals(
            $hash,
            sha1($user->getEmailForVerification())
        )) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        // Jika email sudah diverifikasi sebelumnya
        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('login')
                ->with('verified', 'Email akun ini sudah terverifikasi.');
        }

        // Tandai email sebagai terverifikasi
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Setelah berhasil, arahkan ke login
        return redirect()
            ->route('login')
            ->with('verified', 'Email berhasil diverifikasi. Silakan login.');
    }
}
