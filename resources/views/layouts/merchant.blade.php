@extends('layouts.app')

@section('body')

    <div class="min-h-screen flex">

        @include('components.sidebar.merchant')

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
