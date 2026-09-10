@extends('layouts.admin')

@section('header')

    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Lokasi Merchant
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Pantau lokasi seluruh merchant yang terdaftar di platform PesanIn.
        </p>
    </div>

@endsection


@section('content')

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

    <div>
        <h2 class="text-lg font-extrabold text-slate-900">
            Peta Lokasi Merchant
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Klik marker untuk melihat informasi toko.
        </p>
    </div>


    {{-- Search Merchant --}}
    <div class="flex gap-2">

        <div class="relative">

            <i
                class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">
            </i>

            <input
                type="text"
                id="merchantSearch"
                placeholder="Cari merchant..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/10 sm:w-56"
            >

        </div>


        <button
            type="button"
            id="showAllMerchants"
            class="shrink-0 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-extrabold text-slate-700 transition hover:bg-slate-200"
        >
            Semua
        </button>

    </div>

</div>


        {{-- Map Card --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Map --}}
            <div
                id="merchantLocationMap"
                class="h-[600px] w-full">
            </div>

        </div>

    </div>

@endsection


@push('styles')

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />

@endpush


@push('scripts')

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="{{ asset('js/super_admin/merchant_location.js') }}"></script>

    <script>
        window.merchantLocations = @json($merchants);
    </script>

@endpush