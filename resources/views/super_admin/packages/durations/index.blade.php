@extends('layouts.admin')

@section('content')
    <div class="p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">

            <div>

                <div class="flex items-center gap-3">

                    <a href="{{ route('super_admin.packages.index') }}"
                        class="inline-flex items-center justify-center
                               w-9 h-9
                               bg-white border border-gray-300
                               hover:bg-gray-100
                               text-gray-600
                               rounded-lg
                               transition duration-200"
                        title="Kembali ke Paket">

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
                            Kelola Durasi Paket
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Kelola durasi dan harga untuk paket
                            <span class="font-semibold text-gray-700">
                                {{ $package->name }}
                            </span>.
                        </p>

                    </div>

                </div>

            </div>


            {{-- Add Duration --}}
            <a href="{{ route('super_admin.packages.durations.create', $package) }}"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5
                       bg-indigo-600 hover:bg-indigo-700
                       text-white text-sm font-medium
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
                        d="M12 4v16m8-8H4" />

                </svg>

                Tambah Durasi

            </a>

        </div>


        {{-- Success Message --}}
        @if (session('success'))

            <div class="mb-6 flex items-center gap-3 p-4
                        bg-green-50 border border-green-200
                        text-green-700 rounded-lg">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 flex-shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 13l4 4L19 7" />

                </svg>

                <span class="text-sm font-medium">
                    {{ session('success') }}
                </span>

            </div>

        @endif


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


        {{-- Package Information --}}
        <div class="mb-6 bg-white border border-gray-200
                    rounded-xl shadow-sm">

            <div class="p-5">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-4">

                    <div>

                        <p class="text-xs font-medium
                                  uppercase tracking-wide
                                  text-gray-400">
                            Paket
                        </p>

                        <div class="flex items-center gap-3 mt-1">

                            <h2 class="text-xl font-bold text-gray-800">
                                {{ $package->name }}
                            </h2>

                            @if ($package->status === 'active')

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1
                                             text-xs font-semibold
                                             rounded-full
                                             bg-green-100 text-green-700">

                                    <span class="w-1.5 h-1.5
                                                 rounded-full
                                                 bg-green-500">
                                    </span>

                                    Active

                                </span>

                            @else

                                <span class="inline-flex items-center gap-1.5
                                             px-2.5 py-1
                                             text-xs font-semibold
                                             rounded-full
                                             bg-gray-100 text-gray-600">

                                    <span class="w-1.5 h-1.5
                                                 rounded-full
                                                 bg-gray-400">
                                    </span>

                                    Inactive

                                </span>

                            @endif

                        </div>

                        @if ($package->description)

                            <p class="mt-2 text-sm text-gray-500">
                                {{ $package->description }}
                            </p>

                        @endif

                    </div>


                    <div class="flex-shrink-0">

                        <span class="inline-flex items-center
                                     px-3 py-1.5
                                     bg-gray-100
                                     text-gray-700
                                     text-sm font-medium
                                     rounded-lg">

                            {{ $durations->total() }} Durasi

                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- Duration Table --}}
        <div class="bg-white border border-gray-200
                    rounded-xl shadow-sm overflow-hidden">

            @if ($durations->count())

                <div class="overflow-x-auto">

                    <table class="w-full text-sm text-left">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           whitespace-nowrap">
                                    No
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           whitespace-nowrap">
                                    Nama Durasi
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           text-center whitespace-nowrap">
                                    Durasi
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           whitespace-nowrap">
                                    Harga
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           whitespace-nowrap">
                                    Harga Diskon
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           text-center whitespace-nowrap">
                                    Status
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           text-center whitespace-nowrap">
                                    Urutan
                                </th>

                                <th scope="col"
                                    class="px-6 py-4
                                           font-semibold text-gray-600
                                           text-center whitespace-nowrap">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($durations as $index => $duration)

                                <tr class="hover:bg-gray-50
                                           transition duration-150">

                                    {{-- Number --}}
                                    <td class="px-6 py-4
                                               text-gray-500
                                               whitespace-nowrap">

                                        {{ $durations->firstItem() + $index }}

                                    </td>


                                    {{-- Duration Name --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">

                                            <div class="w-10 h-10
                                                        rounded-lg
                                                        bg-indigo-100
                                                        flex items-center
                                                        justify-center
                                                        flex-shrink-0">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5 text-indigo-600"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2">

                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                                                </svg>

                                            </div>

                                            <div>

                                                <p class="font-semibold
                                                          text-gray-800">

                                                    {{ $duration->name }}

                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Duration Days --}}
                                    <td class="px-6 py-4 text-center">

                                        <span class="inline-flex items-center
                                                     px-3 py-1
                                                     bg-gray-100
                                                     text-gray-700
                                                     font-medium
                                                     rounded-full">

                                            {{ $duration->duration_days }}
                                            hari

                                        </span>

                                    </td>


                                    {{-- Price --}}
                                    <td class="px-6 py-4">

                                        <span class="font-semibold
                                                     text-gray-800
                                                     whitespace-nowrap">

                                            Rp {{ number_format(
                                                $duration->price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}

                                        </span>

                                    </td>


                                    {{-- Discount Price --}}
                                    <td class="px-6 py-4">

                                        @if ($duration->discount_price !== null)

                                            <div>

                                                <span class="font-semibold
                                                             text-green-600
                                                             whitespace-nowrap">

                                                    Rp {{ number_format(
                                                        $duration->discount_price,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }}

                                                </span>

                                                @if ($duration->price > 0)

                                                    @php
                                                        $discountPercentage =
                                                            (($duration->price - $duration->discount_price)
                                                            / $duration->price) * 100;
                                                    @endphp

                                                    <span class="block mt-0.5
                                                                 text-xs
                                                                 text-green-600">

                                                        Hemat
                                                        {{ round($discountPercentage) }}%

                                                    </span>

                                                @endif

                                            </div>

                                        @else

                                            <span class="text-gray-400">
                                                -
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">

                                        @if ($duration->status === 'active')

                                            <span class="inline-flex items-center
                                                         gap-1.5
                                                         px-2.5 py-1
                                                         text-xs font-semibold
                                                         rounded-full
                                                         bg-green-100
                                                         text-green-700">

                                                <span class="w-1.5 h-1.5
                                                             rounded-full
                                                             bg-green-500">
                                                </span>

                                                Active

                                            </span>

                                        @else

                                            <span class="inline-flex items-center
                                                         gap-1.5
                                                         px-2.5 py-1
                                                         text-xs font-semibold
                                                         rounded-full
                                                         bg-gray-100
                                                         text-gray-600">

                                                <span class="w-1.5 h-1.5
                                                             rounded-full
                                                             bg-gray-400">
                                                </span>

                                                Inactive

                                            </span>

                                        @endif

                                    </td>


                                    {{-- Sort Order --}}
                                    <td class="px-6 py-4 text-center">

                                        <span class="font-medium
                                                     text-gray-700">

                                            {{ $duration->sort_order }}

                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-6 py-4">

                                        <div class="flex items-center
                                                    justify-center gap-2">

                                            {{-- Edit --}}
                                            <a href="{{ route(
                                                'super_admin.packages.durations.edit',
                                                [
                                                    'package' => $package,
                                                    'duration' => $duration
                                                ]
                                            ) }}"
                                                class="inline-flex items-center
                                                       justify-center
                                                       w-9 h-9
                                                       bg-indigo-50
                                                       hover:bg-indigo-100
                                                       text-indigo-600
                                                       rounded-lg
                                                       transition duration-200"
                                                title="Edit Durasi">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2">

                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />

                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />

                                                </svg>

                                            </a>


                                            {{-- Delete --}}
                                            <form action="{{ route(
                                                'super_admin.packages.durations.destroy',
                                                [
                                                    'package' => $package,
                                                    'duration' => $duration
                                                ]
                                            ) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus durasi ini?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex items-center
                                                           justify-center
                                                           w-9 h-9
                                                           bg-red-50
                                                           hover:bg-red-100
                                                           text-red-600
                                                           rounded-lg
                                                           transition duration-200"
                                                    title="Hapus Durasi">

                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="w-5 h-5"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                        stroke-width="2">

                                                        <path stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 01-1-1h-4a1 1 0 00-1 1v3m-4 0h14" />

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
                @if ($durations->hasPages())

                    <div class="px-6 py-4 border-t border-gray-200">

                        {{ $durations->links() }}

                    </div>

                @endif

            @else

                {{-- Empty State --}}
                <div class="p-10 text-center">

                    <div class="flex justify-center mb-4">

                        <div class="w-14 h-14
                                    flex items-center justify-center
                                    bg-gray-100 rounded-full">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-7 h-7 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                            </svg>

                        </div>

                    </div>


                    <h3 class="text-lg font-semibold text-gray-800">
                        Belum ada durasi paket
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Tambahkan durasi dan harga untuk paket
                        <span class="font-medium">
                            {{ $package->name }}
                        </span>.
                    </p>

                </div>

            @endif

        </div>

    </div>
@endsection