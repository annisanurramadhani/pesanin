<x-guest-layout>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-7">
            <h2 class="text-2xl font-extrabold text-[#111827]">
                Reset Password
            </h2>

            <p class="mt-1.5 text-sm text-slate-500">
                Buat password baru untuk akun Anda.
            </p>
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="mb-2 text-sm font-bold text-slate-700" />

            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus
                autocomplete="username" placeholder="Masukkan email"
                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password Baru')" class="mb-2 text-sm font-bold text-slate-700" />

            <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="Masukkan password baru"
                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20" />

            <p class="mt-2 text-xs text-slate-500">
                Password harus mengandung huruf, angka, dan simbol.
            </p>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')"
                class="mb-2 text-sm font-bold text-slate-700" />

            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="Masukkan ulang password"
                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-3">
            <button type="submit"
                class="flex items-center gap-2 rounded-xl bg-[#111827] px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                {{ __('Reset Password') }}

                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>

    </form>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Tautan Terkirim',
                    text: @json(session('success')),
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#111827'
                });
            });
        </script>
    @endif
    @if ($errors->has('email'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Permintaan Gagal',
                    text: @json($errors->first('email')),
                    confirmButtonText: 'Coba Lagi',
                    confirmButtonColor: '#111827'
                });
            });
        </script>
    @endif

</x-guest-layout>
