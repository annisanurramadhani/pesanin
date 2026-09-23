<script>
    (function() {
        if (
            localStorage.getItem('pesanin_admin_sidebar_collapsed') === 'true'
        ) {
            document.documentElement.classList.add('sidebar-precollapsed');
        }
    })();
</script>
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<aside id="sidebar"
    class="w-64 bg-[#111827] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen border-r border-slate-800 shadow-2xl transition-all duration-300 ease-in-out">

    <div>

        <div class="px-5 py-5 flex items-center justify-between border-b border-slate-800/80">

            {{-- LOGO + BRAND --}}
            <div id="sidebarLogo" class="flex items-center gap-3 overflow-hidden transition-all duration-300">

                @php
                    $websiteSetting = \App\Models\WebsiteSetting::first();
                @endphp

                <div class="w-10 h-10 shrink-0 flex items-center justify-center">

                    <img src="{{ menuImage($websiteSetting?->logo) }}" alt="PesanIn"
                        class="w-10 h-10 rounded-lg object-cover">

                </div>

                <div id="sidebarBrand" class="whitespace-nowrap transition-all duration-300">

                    <h1 class="font-bold text-white text-base tracking-wide">
                        Command Center
                    </h1>

                    <p class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold">
                        PesanIn Dashboard
                    </p>

                </div>

            </div>


            {{-- HAMBURGER --}}
            <button type="button" id="sidebarToggle"
                class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-all duration-200"
                title="Toggle Sidebar">

                <i id="sidebarToggleIcon" class="fa-solid fa-bars text-lg transition-transform duration-300">
                </i>

            </button>

        </div>

        <nav class="px-4 py-6 space-y-2">

            {{-- dashboard --}}
            <a href="{{ route('super_admin.dashboard') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.dashboard')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-chart-pie w-5 shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Dashboard</span>
            </a>

            {{-- kelola merchant --}}
            <a href="{{ route('super_admin.merchants.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.merchants.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-store w-5 shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Kelola Merchant
                </span>
            </a>

            {{-- ==========================================
            KEUANGAN
            - Rekening Merchant
            - Penarikan Saldo
            ========================================== --}}
            @php
                $isKeuanganActive =
                    request()->routeIs('super_admin.merchant_bank_accounts.*') ||
                    request()->routeIs('super_admin.withdrawals.*');
            @endphp

            <div x-data="{ open: @js($isKeuanganActive) }" class="relative">

                {{-- PARENT MENU KEUANGAN --}}
                <button type="button" @click="open = !open"
                    class="sidebar-menu w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ $isKeuanganActive
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                    <div class="flex items-center gap-3 min-w-0">

                        <i class="fa-solid fa-wallet w-5 shrink-0"></i>

                        <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                            Keuangan
                        </span>

                    </div>

                    <i class="sidebar-chevron fa-solid fa-chevron-down text-xs shrink-0 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"></i>

                </button>


                {{-- SUB MENU KEUANGAN --}}
                <div x-cloak x-show="open" x-transition class="ml-4 pl-4 border-l border-slate-700 space-y-1">

                    {{-- REKENING MERCHANT --}}
                    <a href="{{ route('super_admin.merchant_bank_accounts.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200
                    {{ request()->routeIs('super_admin.merchant_bank_accounts.*')
                        ? 'bg-amber-500/15 text-amber-400'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                        <i class="fa-solid fa-building-columns w-4 shrink-0"></i>

                        <span class="sidebar-text whitespace-nowrap">
                            Rekening Merchant
                        </span>

                    </a>


                    {{-- PENARIKAN SALDO --}}
                    <a href="{{ route('super_admin.withdrawals.index') }}"
                        class="flex items-center gap-3 px-4 py-2.5 rounded-lg font-semibold text-sm transition-all duration-200
                        {{ request()->routeIs('super_admin.withdrawals.*')
                            ? 'bg-amber-500/15 text-amber-400'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                        <i class="fa-solid fa-money-bill-transfer w-4 shrink-0"></i>

                        <span class="sidebar-text whitespace-nowrap">
                            Penarikan Saldo
                        </span>

                    </a>

                </div>

            </div>

            {{-- kelola paket --}}
            <a href="{{ route('super_admin.packages.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
            {{ request()->routeIs('super_admin.packages.*')
                ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-box-open w-5 shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Kelola Paket
                </span>
            </a>

            {{-- kelola langganan --}}
            <a href="{{ route('super_admin.subscriptions.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.subscriptions.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-credit-card w-5 shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Kelola Langganan
                </span>
            </a>

            {{-- kelola akun --}}
            <a href="{{ route('super_admin.accounts.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.accounts.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-users w-5 shrink-0"></i>
                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Kelola Akun
                </span>
            </a>

            {{-- kelola diskon --}}
            <a href="{{ route('super_admin.subscription_promotions.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.subscription_promotions.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                <i class="fa-solid fa-percent w-5 shrink-0"></i>

                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Kelola Diskon
                </span>

            </a>

            {{-- lokasi merchant --}}
            <a href="{{ route('super_admin.merchant_locations.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.merchant_locations.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                <i class="fa-solid fa-map-location-dot w-5 shrink-0"></i>

                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Lokasi Merchant
                </span>

            </a>

            {{-- pengaturan website --}}
            <a href="{{ route('super_admin.settings.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.settings.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                <i class="fa-solid fa-gear w-5 shrink-0"></i>

                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Pengaturan Website
                </span>

            </a>

            {{-- Audit Log --}}
            <a href="{{ route('super_admin.audit_logs.index') }}"
                class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                {{ request()->routeIs('super_admin.audit_logs.*')
                    ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">

                <i class="fa-solid fa-clock-rotate-left w-5 shrink-0"></i>

                <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                    Audit Log
                </span>

            </a>

        </nav>

    </div>

    <div class="p-4 border-t border-slate-800/80">

        <div class="flex items-center justify-between bg-slate-900/80 p-3 rounded-xl border border-slate-800">

            <div class="flex items-center gap-3 overflow-hidden">

                <div
                    class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="truncate sidebar-user-info">

                    <p class="text-xs font-bold text-white truncate">
                        {{ Auth::user()->name }}
                    </p>

                    <span
                        class="inline-block text-[9px] font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>

                </div>

            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 transition" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>

            </form>

        </div>

    </div>

</aside>
<script src="{{ asset('js/super_admin/sidebar.js') }}"></script>
