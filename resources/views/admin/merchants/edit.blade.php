<x-app-layout>

    <div class="max-w-5xl mx-auto space-y-6">

        <div class="flex items-center gap-4">

            <a href="{{ route('admin.merchants.index') }}"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    Edit Merchant
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Perbarui informasi merchant.
                </p>
            </div>

        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                        <i class="fa-solid fa-store"></i>
                    </div>

                    <div>
                        <h2 class="font-extrabold text-slate-900">
                            Informasi Merchant
                        </h2>

                        <p class="text-xs text-slate-400">
                            Perbarui data merchant dengan benar.
                        </p>
                    </div>

                </div>

            </div>

            <form id="merchantForm"
                action="{{ route('admin.merchants.update', [
                    'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $merchant->id),
                ]) }}"
                method="POST" class="p-6" novalidate>

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div class="md:col-span-2">

                        <label for="name" class="mb-2 block text-sm font-bold text-slate-700">
                            Nama Merchant
                        </label>

                        <input type="text" id="name" name="name" value="{{ old('name', $merchant->name) }}"
                            placeholder="Contoh: Kopi PST" autofocus
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                    </div>

                    <div>

                        <label for="phone" class="mb-2 block text-sm font-bold text-slate-700">
                            Nomor Telepon
                        </label>

                        <input type="text" id="phone" name="phone" value="{{ old('phone', $merchant->phone) }}"
                            maxlength="20" inputmode="numeric" placeholder="08xxxxxxxxxx"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                    </div>

                    <div>

                        <label for="subscription_expires_at" class="mb-2 block text-sm font-bold text-slate-700">
                            Masa Langganan
                        </label>

                        <input type="date" id="subscription_expires_at" name="subscription_expires_at"
                            value="{{ old('subscription_expires_at', optional($merchant->subscription_expires_at)->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                    </div>

                    <div class="md:col-span-2">

                        <label for="address" class="mb-2 block text-sm font-bold text-slate-700">
                            Alamat Merchant
                        </label>

                        <textarea id="address" name="address" rows="4" placeholder="Masukkan alamat lengkap merchant..."
                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">{{ old('address', $merchant->address) }}</textarea>

                    </div>

                    <div class="md:col-span-2">

                        <div
                            class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>

                                <div>

                                    <p class="text-sm font-bold text-slate-800">
                                        Status Merchant
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Merchant dapat menggunakan sistem jika status aktif.
                                    </p>

                                </div>

                            </div>

                            <label class="relative inline-flex cursor-pointer items-center">

                                <input type="hidden" name="is_active" value="0">

                                <input type="checkbox" name="is_active" value="1" class="peer sr-only"
                                    {{ old('is_active', $merchant->is_active) ? 'checked' : '' }}>

                                <div
                                    class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-emerald-500 peer-focus:ring-4 peer-focus:ring-emerald-500/20 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-full peer-checked:after:border-white">
                                </div>

                            </label>

                        </div>

                    </div>

                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                    <a href="{{ route('admin.merchants.index') }}"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                        Batal
                    </a>

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-5 py-3 text-sm font-extrabold text-slate-950 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('js/admin/merchant.js') }}"></script>
    @endpush

</x-app-layout>
