<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf

    <div class="mb-7">
        <h2 class="text-2xl font-extrabold text-[#111827]">
            Selamat Datang
        </h2>

        <p class="mt-1.5 text-sm text-slate-500">
            Masuk untuk melanjutkan ke dashboard.
        </p>
    </div>

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

    <div class="flex items-center">
        <label for="remember_me" class="inline-flex items-center gap-2.5 cursor-pointer">

            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500"
            >

            <span class="text-sm font-medium text-slate-600">
                {{ __('Remember me') }}
            </span>

        </label>
    </div>

    <div class="flex items-center justify-between gap-4 pt-3">

        @if (Route::has('password.request'))
            <a
                href="{{ route('password.request') }}"
                class="text-sm font-semibold text-slate-600 underline underline-offset-4 hover:text-amber-600 transition"
            >
                {{ __('Forgot your password?') }}
            </a>
        @endif

        <button
            type="submit"
            class="flex items-center gap-2 rounded-xl bg-[#111827] px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
        >
            {{ __('Log in') }}

            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>

    </div>

</form>
</x-guest-layout>
