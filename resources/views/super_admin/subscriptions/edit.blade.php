@extends('layouts.admin')

@section('header')
    <div class="flex items-center gap-4">

        <a href="{{ route('super_admin.subscriptions.index') }}"
            class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <div>

            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Edit Langganan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi langganan merchant.
            </p>

        </div>

    </div>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-6 py-5">

                <h2 class="font-extrabold text-slate-900">
                    Informasi Langganan
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Perbarui data langganan sesuai kebutuhan.
                </p>

            </div>

            <form
                action="{{ route('super_admin.subscriptions.update', [
                    'encryptedId' => \Illuminate\Support\Facades\Crypt::encryptString((string) $subscription->id),
                ]) }}"
                method="POST" class="p-6">

                @csrf

                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    <div class="md:col-span-2">

                        <label for="merchant_id" class="mb-2 block text-sm font-bold text-slate-700">
                            Merchant
                        </label>

                        <select id="merchant_id" name="merchant_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                            @foreach ($merchants as $merchant)
                                <option value="{{ $merchant->id }}"
                                    {{ old('merchant_id', $subscription->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                    {{ $merchant->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('merchant_id')
                            <p class="mt-1 text-xs font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="md:col-span-2">

                        <label for="package_duration_id" class="mb-2 block text-sm font-bold text-slate-700">
                            Paket dan Durasi
                        </label>

                        <select id="package_duration_id" name="package_duration_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                            @foreach ($packageDurations as $duration)
                                <option value="{{ $duration->id }}"
                                    data-price="{{ $duration->discount_price ?? $duration->price }}"
                                    data-days="{{ $duration->duration_days }}"
                                    {{ old('package_duration_id', $subscription->package_duration_id) == $duration->id ? 'selected' : '' }}>
                                    {{ $duration->package->name ?? '-' }}
                                    - {{ $duration->name }}
                                    - Rp {{ number_format($duration->discount_price ?? $duration->price, 0, ',', '.') }}
                                </option>
                            @endforeach

                        </select>

                        @error('package_duration_id')
                            <p class="mt-1 text-xs font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label for="start_date" class="mb-2 block text-sm font-bold text-slate-700">
                            Tanggal Mulai
                        </label>

                        <input type="date" id="start_date" name="start_date"
                            value="{{ old('start_date', $subscription->start_date?->format('Y-m-d')) }}"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                        @error('start_date')
                            <p class="mt-1 text-xs font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label for="end_date" class="mb-2 block text-sm font-bold text-slate-700">
                            Tanggal Berakhir
                        </label>

                        <input type="date" id="end_date" name="end_date"
                            value="{{ old('end_date', $subscription->end_date?->format('Y-m-d')) }}" readonly
                            class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 outline-none">

                        @error('end_date')
                            <p class="mt-1 text-xs font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label for="price" class="mb-2 block text-sm font-bold text-slate-700">
                            Harga Langganan
                        </label>

                        <input type="date" id="end_date" name="end_date"
                            value="{{ old('end_date', $subscription->end_date?->format('Y-m-d')) }}" readonly
                            class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 outline-none">

                        @error('price')
                            <p class="mt-1 text-xs font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label for="status" class="mb-2 block text-sm font-bold text-slate-700">
                            Status Langganan
                        </label>

                        <select id="status" name="status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">

                            <option value="active"
                                {{ old('status', $subscription->status) === 'active' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="expired"
                                {{ old('status', $subscription->status) === 'expired' ? 'selected' : '' }}>
                                Kedaluwarsa
                            </option>

                            <option value="cancelled"
                                {{ old('status', $subscription->status) === 'cancelled' ? 'selected' : '' }}>
                                Dibatalkan
                            </option>

                        </select>

                        @error('status')
                            <p class="mt-1 text-xs font-semibold text-rose-500">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">

                    <a href="{{ route('super_admin.subscriptions.index') }}"
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
@endsection
