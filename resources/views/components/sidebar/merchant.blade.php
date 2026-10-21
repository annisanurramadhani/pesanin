<script>
    if (
        localStorage.getItem('pesanin_sidebar_collapsed') === 'true'
    ) {
        document.documentElement.classList.add('sidebar-precollapsed');
    }
</script>

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

    html.sidebar-precollapsed .sidebar-section {
        display: none !important;
    }

    html.sidebar-precollapsed .sidebar-menu {
        justify-content: center !important;
    }

    html.sidebar-precollapsed .sidebar-menu>i {
        margin-left: auto !important;
        margin-right: auto !important;
    }
</style>

<aside id="sidebar"
    class="w-64 bg-[#111827] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen border-r border-slate-800 shadow-2xl transition-all duration-300 ease-in-out">

    {{-- HEADER --}}
    <div>

        <div class="px-5 py-5 flex items-center justify-between border-b border-slate-800/80">

            {{-- LOGO PESANIN --}}
            <div id="sidebarLogo" class="flex items-center gap-3 overflow-hidden transition-all duration-300">
                <div class="w-10 h-10 shrink-0 flex items-center justify-center">

                    @if (Auth::user()->merchant && Auth::user()->merchant->logo)
                        <img src="{{ asset('storage/' . Auth::user()->merchant->logo) }}"
                            alt="{{ Auth::user()->merchant->name }}" class="w-10 h-10 rounded-lg object-cover">
                    @else
                        <img src="{{ asset('assets/images/menu-default.jpg') }}" alt="PesanYuk"
                            class="w-10 h-10 rounded-lg object-cover">
                    @endif

                </div>

                <div id="sidebarBrand" class="whitespace-nowrap transition-all duration-300">
                    <h1 class="font-bold text-white text-base tracking-wide">
                        {{ Auth::user()->merchant->name ?? 'PesanYuk' }}
                    </h1>

                    <p class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold">
                        Merchant Dashboard
                    </p>
                </div>
            </div>

            {{-- HAMBURGER --}}
            <button type="button" id="sidebarToggle"
                class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-all duration-200"
                title="Toggle Sidebar">
                <i id="sidebarToggleIcon" class="fa-solid fa-bars text-lg transition-transform duration-300"></i>
            </button>

        </div>


        {{-- NAVIGATION --}}
        <nav class="px-4 py-6 space-y-2">

            {{-- DASHBOARD --}}
            @if (in_array(Auth::user()->role, ['owner', 'kasir']))
                <a href="{{ route('merchant.dashboard') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.dashboard')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-chart-pie w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Dashboard
                    </span>
                </a>
            @endif


            {{-- KELOLA PESANAN --}}
            @if (in_array(Auth::user()->role, ['owner', 'kasir', 'dapur']))
                <a href="{{ route('merchant.orders.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.orders.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-cart-shopping w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Riwayat Pesanan
                    </span>
                </a>
            @endif


            {{-- PENGATURAN KAFE (Hanya Owner) --}}
            @if (Auth::user()->role === 'owner')
                <div class="pt-4 pb-1 sidebar-section">
                    <p
                        class="px-4 text-[10px] font-black uppercase tracking-wider text-slate-500 sidebar-text whitespace-nowrap">
                        Pengaturan Kafe
                    </p>
                </div>

                {{-- QR CODE --}}
                <a href="{{ route('merchant.qr.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.qr.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-qrcode w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola QR Code
                    </span>
                </a>

                {{-- MENU --}}
                <a href="{{ route('merchant.menu.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.menu.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-utensils w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola Menu
                    </span>
                </a>

                {{-- STAF --}}
                <a href="{{ route('merchant.staff.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.staff.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-users-gear w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola Staf
                    </span>
                </a>

                {{-- VOUCHER --}}
                <a href="{{ route('merchant.voucher.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.voucher.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-ticket w-5 shrink-0"></i>

                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola Voucher
                    </span>
                </a>


                {{-- KEUANGAN --}}
                @php
                    $financeMenuActive = request()->routeIs('merchant.kyc.*', 'merchant.withdrawals.*');
                @endphp

                <div class="space-y-1">
                    {{-- MENU INDUK --}}
                    <button type="button" id="financeMenuToggle"
                        aria-expanded="{{ $financeMenuActive ? 'true' : 'false' }}" aria-controls="financeSubmenu"
                        class="sidebar-menu w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
        {{ $financeMenuActive
            ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
            : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-wallet w-5 shrink-0"></i>

                        <span class="sidebar-text flex-1 text-left whitespace-nowrap transition-all duration-200">
                            Keuangan
                        </span>

                        <i id="financeMenuChevron"
                            class="fa-solid fa-chevron-down text-xs transition-transform duration-200
            {{ $financeMenuActive ? 'rotate-180' : '' }}"></i>
                    </button>

                    {{-- SUBMENU --}}
                    <div id="financeSubmenu" class="{{ $financeMenuActive ? '' : 'hidden' }} space-y-1 pl-4">
                        {{-- KYC --}}
                        <a href="{{ route('merchant.kyc.index') }}"
                            class="sidebar-menu flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200
            {{ request()->routeIs('merchant.kyc.*')
                ? 'bg-amber-500 text-slate-950'
                : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-id-card w-5 shrink-0"></i>
                            <span class="sidebar-text whitespace-nowrap">KYC</span>
                        </a>

                        {{-- PENARIKAN SALDO --}}
                        <a href="{{ route('merchant.withdrawals.index') }}"
                            class="sidebar-menu flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200
            {{ request()->routeIs('merchant.withdrawals.*')
                ? 'bg-amber-500 text-slate-950'
                : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="fa-solid fa-money-bill-transfer w-5 shrink-0"></i>
                            <span class="sidebar-text whitespace-nowrap">Penarikan Saldo</span>
                        </a>
                    </div>
                </div>

                <script>
                    (() => {
                        const toggle = document.getElementById('financeMenuToggle');
                        const submenu = document.getElementById('financeSubmenu');
                        const chevron = document.getElementById('financeMenuChevron');

                        if (!toggle || !submenu || !chevron) return;

                        toggle.addEventListener('click', () => {
                            const isOpen = !submenu.classList.contains('hidden');

                            submenu.classList.toggle('hidden', isOpen);
                            chevron.classList.toggle('rotate-180', !isOpen);
                            toggle.setAttribute('aria-expanded', String(!isOpen));
                        });
                    })();
                </script>


                {{-- PENGATURAN --}}
                <a href="{{ route('merchant.settings.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.settings.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-gear w-5 shrink-0"></i>

                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Pengaturan
                    </span>
                </a>
            @endif

        </nav>

    </div>


    {{-- USER PROFILE & LOGOUT --}}
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
                <button type="submit"
                    class="p-1.5 text-slate-400 hover:text-rose-400 transition shrink-0 cursor-pointer" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>

        </div>
    </div>

</aside>


<script src="{{ asset('js/merchant/sidebar.js') }}"></script>
