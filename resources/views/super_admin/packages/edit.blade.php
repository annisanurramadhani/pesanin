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

                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Edit Package
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Update package information for merchants.
                    </p>
                </div>

            </div>
        </div>


        {{-- Success Message --}}
        @if (session('success'))
            <div
                class="mb-6 flex items-center gap-3 p-4
                        bg-green-50 border border-green-200
                        text-green-700 rounded-lg">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />

                </svg>

                <span class="text-sm font-medium">
                    {{ session('success') }}
                </span>

            </div>
        @endif


        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">

                <p class="text-sm font-semibold text-red-700 mb-2">
                    Please fix the following errors:
                </p>

                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>
        @endif


        <div class="max-w-4xl space-y-6">

            {{-- Package Information --}}
            <form action="{{ route('super_admin.packages.update', encryptId($package->id)) }}" method="POST"
                class="package-form">
                @csrf
                @method('PUT')

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

                    {{-- Section Header --}}
                    <div class="px-6 py-5 border-b border-gray-200">

                        <div class="flex items-center justify-between gap-4">

                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">
                                    Package Information
                                </h2>

                                <p class="mt-1 text-sm text-gray-500">
                                    Update the basic information of this package.
                                </p>
                            </div>

                            {{-- Package Status --}}
                            @if ($package->status === 'active')
                                <span
                                    class="px-3 py-1 text-xs font-semibold
                                             rounded-full
                                             bg-green-100 text-green-700">
                                    Active
                                </span>
                            @else
                                <span
                                    class="px-3 py-1 text-xs font-semibold
                                             rounded-full
                                             bg-gray-100 text-gray-600">
                                    Inactive
                                </span>
                            @endif

                        </div>

                    </div>


                    <div class="p-6 space-y-6">

                        {{-- Package Name --}}
                        <div>

                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Package Name
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}"
                                maxlength="100" required
                                class="w-full px-4 py-2.5
                                       border border-gray-300 rounded-lg
                                       text-sm text-gray-800
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

                            <input type="text" name="badge" id="badge" value="{{ old('badge', $package->badge) }}"
                                maxlength="50" placeholder="e.g. Popular, Recommended"
                                class="w-full px-4 py-2.5
                                       border border-gray-300 rounded-lg
                                       text-sm text-gray-800
                                       placeholder-gray-400
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:border-indigo-500
                                       @error('badge') border-red-500 @enderror">

                            @error('badge')
                                <p class="mt-1.5 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Description --}}
                        <div>

                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>

                            <textarea name="description" id="description" rows="4" placeholder="Describe what this package offers..."
                                class="w-full px-4 py-2.5
                                       border border-gray-300 rounded-lg
                                       text-sm text-gray-800
                                       placeholder-gray-400
                                       resize-none
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:border-indigo-500
                                       @error('description') border-red-500 @enderror">{{ old('description', $package->description) }}</textarea>

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

                                    <option value="active"
                                        {{ old('status', $package->status) === 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        {{ old('status', $package->status) === 'inactive' ? 'selected' : '' }}>
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

                                <input type="number" name="sort_order" id="sort_order"
                                    value="{{ old('sort_order', $package->sort_order) }}" min="0" required
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


                        {{-- Slug Information --}}
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">

                            <div class="flex items-center justify-between gap-4">

                                <span class="text-sm font-medium text-gray-600">
                                    Current Slug
                                </span>

                                <code class="text-sm text-gray-700">
                                    {{ $package->slug }}
                                </code>

                            </div>

                            <p class="mt-2 text-xs text-gray-500">
                                The slug is automatically regenerated when the package name changes.
                            </p>

                        </div>

                    </div>


                    {{-- Actions --}}
                    <div
                        class="px-6 py-4 bg-gray-50 border-t border-gray-200
            flex items-center justify-end gap-3">

                        <a href="{{ route('super_admin.packages.index') }}"
                            class="px-4 py-2.5
               bg-white border border-gray-300
               hover:bg-gray-100
               text-gray-700 text-sm font-medium
               rounded-lg transition duration-200">
                            Cancel
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

                            Save Changes

                        </button>

                    </div>
                </div>

            </form>


            {{-- Package Summary --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

                <div class="px-6 py-5 border-b border-gray-200">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Package Summary
                    </h2>

                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">
                                Package
                            </p>

                            <p class="mt-1 text-lg font-semibold text-gray-800">
                                {{ $package->name }}
                            </p>
                        </div>


                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">
                                Durations
                            </p>

                            <p class="mt-1 text-lg font-semibold text-gray-800">
                                {{ $package->durations->count() }}
                            </p>
                        </div>


                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-500">
                                Status
                            </p>

                            <p
                                class="mt-1 text-lg font-semibold
                                {{ $package->status === 'active' ? 'text-green-600' : 'text-gray-500' }}">
                                {{ ucfirst($package->status) }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/package.js') }}"></script>
@endpush
