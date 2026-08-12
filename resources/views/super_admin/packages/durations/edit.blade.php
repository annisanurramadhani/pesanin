@extends('layouts.admin')

@section('content')
    <div class="p-6">

        {{-- Header --}}
        <div class="mb-6">

            <div class="flex items-center gap-3">

                <a href="{{ route('super_admin.packages.durations.index', $package) }}"
                    class="inline-flex items-center justify-center
                           w-9 h-9
                           bg-white border border-gray-300
                           hover:bg-gray-100
                           text-gray-600
                           rounded-lg
                           transition duration-200"
                    title="Kembali">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19l-7-7 7-7" />

                    </svg>

                </a>

                <div>

                    <h1 class="text-2xl font-bold text-gray-800">
                        Edit Durasi Paket
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Perbarui informasi durasi untuk paket
                        <span class="font-semibold text-gray-700">
                            {{ $package->name }}
                        </span>.
                    </p>

                </div>

            </div>

        </div>


        {{-- Validation Errors --}}
        @if ($errors->any())

            <div class="mb-6 p-4
                        bg-red-50 border border-red-200
                        rounded-lg">

                <p class="text-sm font-semibold text-red-700 mb-2">
                    Silakan perbaiki kesalahan berikut:
                </p>

                <ul class="list-disc list-inside
                           text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Form --}}
        <form action="{{ route(
            'super_admin.packages.durations.update',
            [
                'package' => $package,
                'duration' => $duration
            ]
        ) }}"
            method="POST">

            @csrf
            @method('PUT')


            <div class="bg-white border border-gray-200
                        rounded-xl shadow-sm overflow-hidden">

                {{-- Form Header --}}
                <div class="px-6 py-5 border-b border-gray-200">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Informasi Durasi
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Perbarui informasi durasi dan harga berlangganan.
                    </p>

                </div>


                {{-- Form Body --}}
                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Duration Name --}}
                        <div>

                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-700">

                                Nama Durasi
                                <span class="text-red-500">*</span>

                            </label>

                            <input type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $duration->name) }}"
                                placeholder="Contoh: Monthly"
                                required
                                class="w-full px-4 py-2.5
                                       bg-white
                                       border border-gray-300
                                       rounded-lg
                                       text-sm text-gray-800
                                       placeholder-gray-400
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-500
                                       focus:border-indigo-500
                                       transition duration-200">

                            <p class="mt-1.5 text-xs text-gray-500">
                                Contoh: Monthly, Quarterly, Yearly.
                            </p>

                        </div>


                        {{-- Duration Days --}}
                        <div>

                            <label for="duration_days"
                                class="block mb-2 text-sm font-medium text-gray-700">

                                Durasi
                                <span class="text-red-500">*</span>

                            </label>

                            <div class="relative">

                                <input type="number"
                                    id="duration_days"
                                    name="duration_days"
                                    value="{{ old('duration_days', $duration->duration_days) }}"
                                    min="1"
                                    required
                                    class="w-full px-4 py-2.5 pr-16
                                           bg-white
                                           border border-gray-300
                                           rounded-lg
                                           text-sm text-gray-800
                                           placeholder-gray-400
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-indigo-500
                                           focus:border-indigo-500
                                           transition duration-200">

                                <span class="absolute right-4 top-1/2
                                             -translate-y-1/2
                                             text-sm text-gray-400">
                                    hari
                                </span>

                            </div>

                        </div>


                        {{-- Price --}}
                        <div>

                            <label for="price"
                                class="block mb-2 text-sm font-medium text-gray-700">

                                Harga Normal
                                <span class="text-red-500">*</span>

                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2
                                             -translate-y-1/2
                                             text-sm text-gray-500">
                                    Rp
                                </span>

                                <input type="number"
                                    id="price"
                                    name="price"
                                    value="{{ old('price', $duration->price) }}"
                                    min="0"
                                    step="0.01"
                                    required
                                    class="w-full px-4 py-2.5 pl-12
                                           bg-white
                                           border border-gray-300
                                           rounded-lg
                                           text-sm text-gray-800
                                           placeholder-gray-400
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-indigo-500
                                           focus:border-indigo-500
                                           transition duration-200">

                            </div>

                            <p class="mt-1.5 text-xs text-gray-500">
                                Harga normal sebelum diskon.
                            </p>

                        </div>


                        {{-- Discount Price --}}
                        <div>

                            <label for="discount_price"
                                class="block mb-2 text-sm font-medium text-gray-700">

                                Harga Diskon

                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2
                                             -translate-y-1/2
                                             text-sm text-gray-500">
                                    Rp
                                </span>

                                <input type="number"
                                    id="discount_price"
                                    name="discount_price"
                                    value="{{ old('discount_price', $duration->discount_price) }}"
                                    min="0"
                                    step="0.01"
                                    placeholder="40000"
                                    class="w-full px-4 py-2.5 pl-12
                                           bg-white
                                           border border-gray-300
                                           rounded-lg
                                           text-sm text-gray-800
                                           placeholder-gray-400
                                           focus:outline-none
                                           focus:ring-2
                                           focus:ring-indigo-500
                                           focus:border-indigo-500
                                           transition duration-200">

                            </div>

                            <p class="mt-1.5 text-xs text-gray-500">
                                Kosongkan jika tidak ada harga diskon.
                            </p>

                        </div>


                        {{-- Sort Order --}}
                        <div>

                            <label for="sort_order"
                                class="block mb-2 text-sm font-medium text-gray-700">

                                Urutan
                                <span class="text-red-500">*</span>

                            </label>

                            <input type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ old('sort_order', $duration->sort_order) }}"
                                min="0"
                                required
                                class="w-full px-4 py-2.5
                                       bg-white
                                       border border-gray-300
                                       rounded-lg
                                       text-sm text-gray-800
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-500
                                       focus:border-indigo-500
                                       transition duration-200">

                            <p class="mt-1.5 text-xs text-gray-500">
                                Semakin kecil angka, semakin awal ditampilkan.
                            </p>

                        </div>


                        {{-- Status --}}
                        <div>

                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-700">

                                Status
                                <span class="text-red-500">*</span>

                            </label>

                            <select id="status"
                                name="status"
                                required
                                class="w-full px-4 py-2.5
                                       bg-white
                                       border border-gray-300
                                       rounded-lg
                                       text-sm text-gray-800
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-indigo-500
                                       focus:border-indigo-500
                                       transition duration-200">

                                <option value="active"
                                    {{ old('status', $duration->status) === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="inactive"
                                    {{ old('status', $duration->status) === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                {{-- Form Footer --}}
                <div class="px-6 py-4
                            bg-gray-50
                            border-t border-gray-200
                            flex items-center justify-end gap-3">

                    <a href="{{ route('super_admin.packages.durations.index', $package) }}"
                        class="px-4 py-2.5
                               bg-white
                               border border-gray-300
                               hover:bg-gray-100
                               text-gray-700
                               text-sm font-medium
                               rounded-lg
                               transition duration-200">

                        Cancel

                    </a>


                    <button type="submit"
                        class="inline-flex items-center gap-2
                               px-5 py-2.5
                               bg-indigo-600
                               hover:bg-indigo-700
                               text-white
                               text-sm font-medium
                               rounded-lg
                               transition duration-200">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />

                        </svg>

                        Save Changes

                    </button>

                </div>

            </div>

        </form>

    </div>
@endsection