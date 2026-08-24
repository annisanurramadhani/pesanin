<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<x-guest-layout>

    {{-- Session Status --}}
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />


    {{-- ================================================================
        LOGIN FORM
    ================================================================= --}}

    <form
        method="POST"
        action="{{ route('login') }}"
        class="space-y-5"
    >

        @csrf


        {{-- ============================================================
            HEADER
        ============================================================= --}}

        <div class="mb-7">

            <h2 class="text-2xl font-extrabold text-[#111827]">
                Selamat Datang
            </h2>

            <p class="mt-1.5 text-sm text-slate-500">
                Masuk untuk melanjutkan ke dashboard.
            </p>

        </div>


        {{-- ============================================================
            EMAIL
        ============================================================= --}}

        <div>

            <x-input-label
                for="email"
                :value="__('Email')"
                class="mb-2 text-sm font-bold text-slate-700"
            />

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan email"
                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />

        </div>


        {{-- ============================================================
            PASSWORD
        ============================================================= --}}

        <div>

            <x-input-label
                for="password"
                :value="__('Password')"
                class="mb-2 text-sm font-bold text-slate-700"
            />

            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />

        </div>


        {{-- ============================================================
            RECAPTCHA
        ============================================================= --}}

        <div>

            <div
                class="g-recaptcha"
                data-sitekey="{{ config('services.recaptcha.site_key') }}"
            ></div>

            <x-input-error
                :messages="$errors->get('g-recaptcha-response')"
                class="mt-2"
            />

        </div>


        {{-- ============================================================
            REMEMBER ME
        ============================================================= --}}

        <div class="flex items-center">

            <label
                for="remember_me"
                class="inline-flex cursor-pointer items-center gap-2.5"
            >

                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500"
                >

                <span class="text-sm font-medium text-slate-600">
                    {{ __('Ingat Saya') }}
                </span>

            </label>

        </div>


        {{-- ============================================================
            ACTION
        ============================================================= --}}

        <div class="flex items-center justify-between gap-4 pt-3">


            {{-- Lupa Password --}}
            @if (Route::has('password.request'))

                <a
                    href="{{ route('password.request') }}"
                    class="text-sm font-semibold text-slate-600 underline underline-offset-4 transition hover:text-amber-600"
                >
                    {{ __('Lupa Password?') }}
                </a>

            @endif


            {{-- Login --}}
            <button
                type="submit"
                class="flex items-center gap-2 rounded-xl bg-[#111827] px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/20 transition-all duration-200 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
            >

                {{ __('Masuk') }}

                <i class="fa-solid fa-arrow-right text-xs"></i>

            </button>

        </div>

    </form>


    {{-- ================================================================
        SUCCESS ALERT
    ================================================================= --}}

    @if (session('success'))

        <script>

            document.addEventListener(
                'DOMContentLoaded',
                function () {

                    Swal.fire({

                        icon: 'success',

                        title: 'Berhasil',

                        text: @json(session('success')),

                        confirmButtonText: 'Mengerti',

                        confirmButtonColor: '#111827'

                    });

                }
            );

        </script>

    @endif


</x-guest-layout>
