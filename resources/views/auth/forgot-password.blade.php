<x-guest-layout>

    <div class="mb-7">
        <h2 class="text-2xl font-extrabold text-[#111827]">
            Lupa Password?
        </h2>

        <p class="mt-1.5 text-sm text-slate-500">
            Masukkan email yang terdaftar untuk mendapatkan tautan
            pengaturan ulang password.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('password.email') }}"
        class="space-y-5"
    >
        @csrf

        <!-- Email -->
        <div>
            <x-input-label
                for="email"
                value="Email"
                class="mb-2 text-sm font-bold text-slate-700"
            />

            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="Masukkan email terdaftar"
                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <!-- Button -->
        <div class="flex items-center justify-between gap-4 pt-3">

            <a
                href="{{ route('login') }}"
                class="text-sm font-semibold text-slate-600 underline underline-offset-4 hover:text-amber-600 transition"
            >
                Kembali ke Login
            </a>

            <button
                type="submit"
                class="flex items-center gap-2 rounded-xl bg-[#111827] px-6 py-3 text-sm font-extrabold text-white shadow-lg shadow-slate-900/20 hover:bg-slate-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
            >
                Kirim Tautan
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>

        </div>

    </form>

</x-guest-layout>
