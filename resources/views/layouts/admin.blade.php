@extends('layouts.app')

@push('styles')
<style>
    html.sidebar-precollapsed #sidebar {
        width: 5rem !important;
    }

    html.sidebar-precollapsed #sidebarBrand,
    html.sidebar-precollapsed .sidebar-text,
    html.sidebar-precollapsed .sidebar-user-info {
        opacity: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
    }

    html.sidebar-precollapsed .sidebar-menu {
        justify-content: center !important;
    }

    html.sidebar-precollapsed .sidebar-menu > i {
        margin-left: auto !important;
        margin-right: auto !important;
    }

    html.sidebar-precollapsed .sidebar-chevron {
        display: none !important;
    }
</style>
@endpush

@section('body')

    <div class="min-h-screen flex">

        @include('components.sidebar.admin')

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            @hasSection('header')
                <header class="bg-white border-b border-slate-200/80 px-8 py-5 sticky top-0 z-10 shadow-sm">
                    @yield('header')
                </header>
            @endif

            <main class="flex-1 p-8">
                @yield('content')
            </main>

        </div>

    </div>

@endsection