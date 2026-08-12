@extends('layouts.admin')

@section('content')
    <div class="p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Kelola Paket
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Kelola paket berlangganan yang tersedia untuk pedagang.
                </p>
            </div>

            <a href="{{ route('super_admin.packages.create') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                       bg-indigo-600 hover:bg-indigo-700
                       text-white text-sm font-medium rounded-lg
                       transition duration-200">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />

                </svg>

                Tambah Paket
            </a>

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
                    Silakan perbaiki kesalahan berikut:
                </p>

                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>
        @endif


        {{-- Package Table --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            @if ($packages->count())
                {{-- Responsive Table --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-sm text-left">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th scope="col" class="px-6 py-4 font-semibold text-gray-600 whitespace-nowrap">
                                    No
                                </th>

                                <th scope="col" class="px-6 py-4 font-semibold text-gray-600 whitespace-nowrap">
                                    Paket
                                </th>

                                <th scope="col" class="px-6 py-4 font-semibold text-gray-600 whitespace-nowrap">
                                    Deskripsi
                                </th>

                                <th scope="col"
                                    class="px-6 py-4 font-semibold text-gray-600 text-center whitespace-nowrap">
                                    Durasi
                                </th>

                                <th scope="col"
                                    class="px-6 py-4 font-semibold text-gray-600 text-center whitespace-nowrap">
                                    Status
                                </th>

                                <th scope="col"
                                    class="px-6 py-4 font-semibold text-gray-600 text-center whitespace-nowrap">
                                    Urutan
                                </th>

                                <th scope="col"
                                    class="px-6 py-4 font-semibold text-gray-600 text-center whitespace-nowrap">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($packages as $index => $package)
                                <tr class="hover:bg-gray-50 transition duration-150">

                                    {{-- Number --}}
                                    <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                        {{ $packages->firstItem() + $index }}
                                    </td>


                                    {{-- Package --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="w-10 h-10 rounded-lg
                                                        bg-indigo-100
                                                        flex items-center justify-center
                                                        flex-shrink-0">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />

                                                </svg>

                                            </div>


                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <p class="font-semibold text-gray-800">
                                                        {{ strip_tags($package->name) }}
                                                    </p>

                                                    @if ($package->badge)
                                                        <span
                                                            class="px-2 py-0.5
                                                                     text-xs font-semibold
                                                                     rounded-full
                                                                     bg-indigo-100
                                                                     text-indigo-700
                                                                     whitespace-nowrap">

                                                            {{ strip_tags($package->badge) }}

                                                        </span>
                                                    @endif

                                                </div>

                                                <p class="mt-0.5 text-xs text-gray-400">
                                                    {{ strip_tags($package->slug) }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Description --}}
                                    <td class="px-6 py-4 max-w-xs">

                                        <p class="text-gray-600 line-clamp-2">
                                            {{ $package->description ? strip_tags($package->description) : 'Tidak ada deskripsi.' }}
                                        </p>
                                        </p>

                                    </td>


                                    {{-- Duration --}}
                                    <td class="px-6 py-4 text-center">

                                        <span
                                            class="inline-flex items-center
                                                     justify-center
                                                     min-w-8 px-2.5 py-1
                                                     rounded-full
                                                     bg-gray-100
                                                     text-gray-700
                                                     font-medium">

                                            {{ $package->durations_count }}

                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">

                                        @if ($package->status === 'active')
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                         px-2.5 py-1
                                                         text-xs font-semibold
                                                         rounded-full
                                                         bg-green-100
                                                         text-green-700">

                                                <span
                                                    class="w-1.5 h-1.5
                                                             rounded-full
                                                             bg-green-500">
                                                </span>

                                                Active

                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5
                                                         px-2.5 py-1
                                                         text-xs font-semibold
                                                         rounded-full
                                                         bg-gray-100
                                                         text-gray-600">

                                                <span
                                                    class="w-1.5 h-1.5
                                                             rounded-full
                                                             bg-gray-400">
                                                </span>

                                                Inactive

                                            </span>
                                        @endif

                                    </td>


                                    {{-- Sort Order --}}
                                    <td class="px-6 py-4 text-center">

                                        <span class="font-medium text-gray-700">
                                            {{ $package->sort_order }}
                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center justify-center gap-2">

                                            {{-- Duration --}}
                                            <a href="{{ route('super_admin.packages.durations.index', encryptId($package->id)) }}"
                                                class="inline-flex items-center justify-center
                                                       w-9 h-9
                                                       bg-gray-100 hover:bg-gray-200
                                                       text-gray-600
                                                       rounded-lg
                                                       transition duration-200"
                                                title="Kelola Durasi">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 8c-2.21 0-4 1.12-4 2.5S9.79 13 12 13s4-1.12 4-2.5S14.21 8 12 8z" />

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 12v4.5C4 18.43 7.58 20 12 20s8-1.57 8-3.5V12" />

                                                </svg>

                                            </a>


                                            {{-- Edit --}}
                                            <a href="{{ route('super_admin.packages.edit', encryptId($package->id)) }}"
                                                class="inline-flex items-center justify-center
                                                       w-9 h-9
                                                       bg-indigo-50 hover:bg-indigo-100
                                                       text-indigo-600
                                                       rounded-lg
                                                       transition duration-200"
                                                title="Edit Paket">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />

                                                </svg>

                                            </a>


                                            {{-- Delete --}}
                                            <form
                                                action="{{ route('super_admin.packages.destroy', encryptId($package->id)) }}"
                                                method="POST" class="delete-package-form">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex items-center justify-center
               w-9 h-9
               bg-red-50 hover:bg-red-100
               text-red-600
               rounded-lg
               transition duration-200"
                                                    title="Hapus Paket">

                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14" />
                                                    </svg>

                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if ($packages->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $packages->links() }}
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="p-10 text-center">

                    <div class="flex justify-center mb-4">

                        <div
                            class="w-14 h-14 flex items-center justify-center
                                    bg-gray-100 rounded-full">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4" />

                            </svg>

                        </div>

                    </div>

                    <h3 class="text-lg font-semibold text-gray-800">
                        Belum ada paket
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Buat paket berlangganan pertama untuk pedagang.
                    </p>

                    <a href="{{ route('super_admin.packages.create') }}"
                        class="inline-flex items-center gap-2 mt-5
                               px-4 py-2.5
                               bg-indigo-600 hover:bg-indigo-700
                               text-white text-sm font-medium
                               rounded-lg transition duration-200">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />

                        </svg>

                        Tambah Paket

                    </a>

                </div>
            @endif

        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/package.js') }}"></script>
@endpush
