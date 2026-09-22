@extends('layouts.admin')

@section('title', 'Audit Log')

@section('content')

<div class="space-y-6">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">
                Audit Log
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau seluruh aktivitas dan perubahan yang terjadi di sistem PesanIn.
            </p>
        </div>

        {{-- Total Aktivitas --}}
        <div class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white px-5 py-3 shadow-sm">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-500">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 12h6"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 16h6"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 8h6"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                    />
                </svg>

            </div>

            <div>
                <p class="text-xs text-slate-400">
                    Total Aktivitas
                </p>

                <p class="text-lg font-bold text-slate-800">
                    {{ number_format($auditLogs->total()) }}
                </p>
            </div>

        </div>

    </div>


    {{-- =========================================================
        FILTER CARD
    ========================================================== --}}
    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">

        <form
            action="{{ route('super_admin.audit_logs.index') }}"
            method="GET"
        >

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-12">

                {{-- Search --}}
                <div class="xl:col-span-4">

                    <label
                        for="search"
                        class="mb-2 block text-xs font-semibold text-slate-700"
                    >
                        Cari aktivitas
                    </label>

                    <div class="relative">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-4.35-4.35"
                                />

                                <circle
                                    cx="11"
                                    cy="11"
                                    r="6"
                                />
                            </svg>

                        </div>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari aktivitas, user, atau ID..."
                            class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        >

                    </div>

                </div>


                {{-- Aktivitas --}}
                <div class="xl:col-span-2">

                    <label
                        for="action"
                        class="mb-2 block text-xs font-semibold text-slate-700"
                    >
                        Aktivitas
                    </label>

                    <div class="relative">

                        <select
                            id="action"
                            name="action"
                            class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-white px-3.5 pr-9 text-sm text-slate-700 outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        >

                            <option value="">
                                Semua aktivitas
                            </option>

                            @foreach ($actions as $action)
                                <option
                                    value="{{ $action }}"
                                    @selected(request('action') === $action)
                                >
                                    {{ ucfirst(strtolower($action)) }}
                                </option>
                            @endforeach

                        </select>

                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 9 6 6 6-6"
                                />
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Data --}}
                <div class="xl:col-span-2">

                    <label
                        for="model"
                        class="mb-2 block text-xs font-semibold text-slate-700"
                    >
                        Data
                    </label>

                    <div class="relative">

                        <select
                            id="model"
                            name="model"
                            class="h-11 w-full appearance-none rounded-xl border border-slate-200 bg-white px-3.5 pr-9 text-sm text-slate-700 outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        >

                            <option value="">
                                Semua data
                            </option>

                            @foreach ($models as $model)
                                <option
                                    value="{{ $model }}"
                                    @selected(request('model') === $model)
                                >
                                    {{ class_basename($model) }}
                                </option>
                            @endforeach

                        </select>

                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m6 9 6 6 6-6"
                                />
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Dari --}}
                <div class="xl:col-span-1">

                    <label
                        for="date_from"
                        class="mb-2 block text-xs font-semibold text-slate-700"
                    >
                        Dari
                    </label>

                    <input
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                    >

                </div>


                {{-- Sampai --}}
                <div class="xl:col-span-1">

                    <label
                        for="date_to"
                        class="mb-2 block text-xs font-semibold text-slate-700"
                    >
                        Sampai
                    </label>

                    <input
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-700 outline-none transition focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                    >

                </div>


                {{-- Filter Button --}}
                <div class="flex items-end xl:col-span-2">

                    <button
                        type="submit"
                        class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-orange-500 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-200"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 5h18M6 12h12m-9 7h6"
                            />
                        </svg>

                        Filter

                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- =========================================================
        AUDIT LOG TABLE CARD
    ========================================================== --}}
    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">

        {{-- Table Header --}}
        <div class="flex flex-col gap-1 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-sm font-semibold text-slate-800">
                    Riwayat Aktivitas
                </h2>

                <p class="mt-0.5 text-xs text-slate-400">
                    Daftar aktivitas terbaru dalam sistem.
                </p>
            </div>

        </div>


        {{-- =========================================================
            TABLE
        ========================================================== --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] table-fixed text-sm">

                {{-- =================================================
                    TABLE HEAD
                ================================================== --}}
                <thead>

                    <tr class="border-b border-slate-100 bg-slate-50/70">

                        {{-- Waktu --}}
                        <th class="w-[15%] px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Waktu
                        </th>

                        {{-- Aksi --}}
                        <th class="w-[15%] px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Aksi
                        </th>

                        {{-- Data --}}
                        <th class="w-[20%] px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Data
                        </th>

                        {{-- Oleh --}}
                        <th class="w-[20%] px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Oleh
                        </th>

                        {{-- Perubahan --}}
                        <th class="w-[30%] px-5 py-3.5 text-left text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            Perubahan
                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                    TABLE BODY
                ================================================== --}}
                <tbody
    id="audit-log-table-body"
    class="divide-y divide-slate-100"
>

                    @forelse ($auditLogs as $log)

                        @php

                            /*
                             * Action
                             */
                            $action = strtolower($log->action ?? '');

                            /*
                             * Model name
                             */
                            $modelName = '-';

                            if ($log->model_type) {
                                $modelName = class_basename($log->model_type);
                            }

                            /*
                             * Model label
                             */
                            $modelLabel = match (strtolower($modelName)) {
                                'order' => 'Order',
                                'delivery' => 'Delivery',
                                'visit' => 'Kunjungan',
                                'merchant' => 'Merchant',
                                'category' => 'Kategori',
                                'menu' => 'Menu',
                                'user' => 'Pengguna',
                                default => $modelName,
                            };

                            /*
                             * Action label
                             */
                            $actionLabel = match ($action) {
                                'create' => 'created',
                                'update' => 'updated',
                                'delete' => 'deleted',
                                default => $log->action ?? '-',
                            };

                            

                            /*
                             * Old & New Values
                             */
                            $oldValues = $log->old_values ?? [];
                            $newValues = $log->new_values ?? [];

                            $changes = [];

                            /*
                             * CREATE
                             */
                            if ($action === 'create') {

                                foreach ($newValues as $key => $value) {

                                    if (is_array($value)) {
                                        $value = json_encode(
                                            $value,
                                            JSON_UNESCAPED_UNICODE
                                        );
                                    }

                                    $changes[] =
                                        $key . ': ' .
                                        ($value === null ? 'null' : $value);
                                }

                            /*
                             * DELETE
                             */
                            } elseif ($action === 'delete') {

                                foreach ($oldValues as $key => $value) {

                                    if (is_array($value)) {
                                        $value = json_encode(
                                            $value,
                                            JSON_UNESCAPED_UNICODE
                                        );
                                    }

                                    $changes[] =
                                        $key . ': ' .
                                        ($value === null ? 'null' : $value);
                                }

                            /*
                             * UPDATE
                             */
                            } else {

                                foreach ($newValues as $key => $newValue) {

                                    $oldValue = $oldValues[$key] ?? null;

                                    if ($oldValue != $newValue) {

                                        if (is_array($oldValue)) {
                                            $oldValue = json_encode(
                                                $oldValue,
                                                JSON_UNESCAPED_UNICODE
                                            );
                                        }

                                        if (is_array($newValue)) {
                                            $newValue = json_encode(
                                                $newValue,
                                                JSON_UNESCAPED_UNICODE
                                            );
                                        }

                                        $changes[] =
                                            $key . ': ' .
                                            ($newValue === null
                                                ? 'null'
                                                : $newValue);
                                    }
                                }
                            }

                            /*
                             * Final change text
                             */
                            $changeText = implode(', ', $changes);

                            /*
                             * User
                             */
                            $userName = $log->user?->name ?? 'System';

                            /*
                             * User Initial
                             */
                            $userInitial = strtoupper(
                                substr($userName, 0, 1)
                            );

                        @endphp


                        {{-- =================================================
                            AUDIT ROW
                        ================================================== --}}
                        <tr class="group transition-colors duration-150 hover:bg-orange-50/30">

                            {{-- =============================================
                                WAKTU
                            ============================================== --}}
                            <td class="px-5 py-4 align-top">

                                <div class="text-xs font-medium text-slate-700">
                                    {{ $log->created_at?->format('d M Y') }}
                                </div>

                                <div class="mt-1 text-[11px] text-slate-400">
                                    {{ $log->created_at?->format('H:i:s') }} WIB
                                </div>

                            </td>


                            {{-- =============================================
                                AKSI
                            ============================================== --}}
                            <td class="px-5 py-4 align-top">

                                <span class="text-xs font-semibold text-slate-800">
                                    {{ $actionLabel }}
                                </span>

                            </td>


                            {{-- =============================================
                                DATA
                            ============================================== --}}
                            <td class="px-5 py-4 align-top">

                                @if ($log->model_type)

                                    <div class="flex items-center gap-2">

                                        {{-- Model Icon --}}
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                                />
                                            </svg>

                                        </div>

                                        <div class="min-w-0">

                                            <div class="text-xs font-semibold text-slate-700">
                                                {{ $modelLabel }}
                                            </div>

                                            @if ($log->model_id)

                                                <div class="mt-0.5 text-[11px] text-slate-400">
                                                    ID #{{ $log->model_id }}
                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                @else

                                    <span class="text-xs text-slate-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- =============================================
                                OLEH
                            ============================================== --}}
                            <td class="px-5 py-4 align-top">

                                <div class="flex items-center gap-2">

                                    {{-- Avatar --}}
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-orange-50 text-xs font-bold text-orange-600">
                                        {{ $userInitial }}
                                    </div>

                                    {{-- User Name --}}
                                    <div class="min-w-0">

                                        <div class="truncate text-xs font-semibold text-slate-700">
                                            {{ $userName }}
                                        </div>

                                        @if ($log->user?->email)

                                            <div class="mt-0.5 max-w-[110px] truncate text-[10px] text-slate-400">
                                                {{ $log->user->email }}
                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- =============================================
                                PERUBAHAN
                            ============================================== --}}
                            <td class="px-5 py-4 align-top">

                                @if ($changeText)

                                    <div
                                        class="max-w-[650px] truncate text-xs leading-5 text-slate-500"
                                        title="{{ $changeText }}"
                                    >
                                        {{ $changeText }}
                                    </div>

                                @else

                                    <span class="text-xs text-slate-400">
                                        Tidak ada perubahan
                                    </span>

                                @endif

                            </td>

                        </tr>


                    @empty

                        {{-- =================================================
                            EMPTY STATE
                        ================================================== --}}
                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-16 text-center"
                            >

                                <div class="mx-auto flex max-w-sm flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-7 w-7"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"
                                            />

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M14 3v5h5"
                                            />
                                        </svg>

                                    </div>

                                    <h3 class="mt-4 text-sm font-semibold text-slate-700">
                                        Belum ada aktivitas
                                    </h3>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Aktivitas sistem akan muncul di sini.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================================================
            PAGINATION
        ========================================================== --}}
        @if ($auditLogs->hasPages())

            <div class="border-t border-slate-100 px-5 py-4">

                {{ $auditLogs->links() }}

            </div>

        @endif

    </div>

</div>

@push('scripts')
    <script src="{{ asset('js/super_admin/audit_log.js') }}"></script>
@endpush

@endsection
