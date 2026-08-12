@extends('layouts.admin')

@section('content')
    <div class="p-6">

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">

                <a href="{{ route('super_admin.packages.index') }}" class="text-gray-500 hover:text-gray-700 transition"
                    title="Back">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />

                    </svg>

                </a>

                <h1 class="text-2xl font-bold text-gray-800">
                    Tambah Paket
                </h1>

            </div>

            <p class="text-sm text-gray-500 ml-8">
                Buat paket berlangganan baru untuk pedagang.
            </p>
        </div>


        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">

                <div class="flex items-start gap-3">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M10.29 3.86l-7.82 14A2 2 0 004.21 21h15.58a2 2 0 001.74-3.14l-7.82-14a2 2 0 00-3.42 0z" />

                    </svg>

                    <div>
                        <p class="text-sm font-semibold text-red-700">
                            Silakan perbaiki kesalahan berikut:
                        </p>

                        <ul class="mt-2 list-disc list-inside text-sm text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>

            </div>
        @endif


        {{-- Form --}}
        <div class="max-w-4xl">

            <form action="{{ route('super_admin.packages.store') }}" method="POST" class="package-form">
                @csrf



                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                    {{-- Section Header --}}
                    <div class="px-6 py-5 border-b border-gray-200">

                        <h2 class="text-lg font-semibold text-gray-800">
                            Informasi Paket
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Enter the basic information for this package.
                        </p>

                    </div>


                    <div class="p-6 space-y-6">

                        {{-- Package Name --}}
                        <div>

                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Paket
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                placeholder="e.g. Silver" required maxlength="100"
                                class="w-full px-4 py-2.5
                                       border border-gray-300 rounded-lg
                                       text-sm text-gray-800
                                       placeholder-gray-400
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:border-indigo-500
                                       @error('name') border-red-500 @enderror">

                            @error('name')
                                <p class="mt-1.5 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Badge --}}
                        <div>

                            <label for="badge" class="block text-sm font-medium text-gray-700 mb-2">
                                Badge
                            </label>

                            <input type="text" name="badge" id="badge" value="{{ old('badge') }}"
                                placeholder="e.g. Popular, Recommended" maxlength="50"
                                class="w-full px-4 py-2.5
                                       border border-gray-300 rounded-lg
                                       text-sm text-gray-800
                                       placeholder-gray-400
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:border-indigo-500
                                       @error('badge') border-red-500 @enderror">

                            <p class="mt-1.5 text-xs text-gray-500">
                                Optional label displayed on the package card.
                            </p>

                            @error('badge')
                                <p class="mt-1.5 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Description --}}
                        <div>

                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>

                            <textarea name="description" id="description" rows="4" placeholder="Describe what this package offers..."
                                class="w-full px-4 py-2.5
                                       border border-gray-300 rounded-lg
                                       text-sm text-gray-800
                                       placeholder-gray-400
                                       resize-none
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:border-indigo-500
                                       @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>

                            @error('description')
                                <p class="mt-1.5 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Status & Sort Order --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Status --}}
                            <div>

                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="status" id="status" required
                                    class="w-full px-4 py-2.5
                                           border border-gray-300 rounded-lg
                                           text-sm text-gray-800
                                           bg-white
                                           focus:outline-none focus:ring-2
                                           focus:ring-indigo-500 focus:border-indigo-500
                                           @error('status') border-red-500 @enderror">

                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>

                                </select>

                                @error('status')
                                    <p class="mt-1.5 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- Sort Order --}}
                            <div>

                                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sort Order
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order') }}"
                                    min="0" required
                                    class="w-full px-4 py-2.5
           border border-gray-300 rounded-lg
           text-sm text-gray-800
           focus:outline-none focus:ring-2
           focus:ring-indigo-500 focus:border-indigo-500
           @error('sort_order') border-red-500 @enderror">

                                <p class="mt-1.5 text-xs text-gray-500">
                                    Lower numbers appear first.
                                </p>

                                @error('sort_order')
                                    <p class="mt-1.5 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- Form Actions --}}
                    <div
                        class="px-6 py-4 bg-gray-50 border-t border-gray-200
                                flex items-center justify-end gap-3">

                        <a href="{{ route('super_admin.packages.index') }}"
                            class="px-4 py-2.5
                                   bg-white border border-gray-300
                                   hover:bg-gray-100
                                   text-gray-700 text-sm font-medium
                                   rounded-lg transition duration-200">
                            Batal
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-2
                                   px-5 py-2.5
                                   bg-indigo-600 hover:bg-indigo-700
                                   text-white text-sm font-medium
                                   rounded-lg transition duration-200">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />

                            </svg>

                            Buat Paket

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/package.js') }}"></script>
@endpush
