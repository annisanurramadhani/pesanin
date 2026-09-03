<aside
    id="sidebar"
    class="w-64 bg-[#111827] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen border-r border-slate-800 shadow-2xl transition-all duration-300 ease-in-out"
>

    {{-- HEADER --}}
    <div>

        <div class="px-5 py-5 flex items-center justify-between border-b border-slate-800/80">

            {{-- LOGO PESANIN --}}
            <div id="sidebarLogo" class="flex items-center gap-3 overflow-hidden transition-all duration-300">
                <div class="w-10 h-10 shrink-0 flex items-center justify-center">

                    @if (Auth::user()->merchant && Auth::user()->merchant->logo)

                        <img
                            src="{{ asset('storage/' . Auth::user()->merchant->logo) }}"
                            alt="{{ Auth::user()->merchant->name }}"
                            class="w-10 h-10 rounded-lg object-cover"
                        >

                    @else

                        <img
                            src="{{ asset('assets/images/menu-default.jpg') }}"
                            alt="PesanIn"
                            class="w-10 h-10 rounded-lg object-cover"
                        >

                    @endif

                </div>

                <div id="sidebarBrand" class="whitespace-nowrap transition-all duration-300">
                    <h1 class="font-bold text-white text-base tracking-wide">
                        {{ Auth::user()->merchant->name ?? 'PesanIn' }}
                    </h1>

                    <p class="text-[10px] text-slate-400 tracking-wider uppercase font-semibold">
                        Merchant Dashboard
                    </p>
                </div>
            </div>

            {{-- HAMBURGER --}}
            <button
                type="button"
                id="sidebarToggle"
                class="w-9 h-9 shrink-0 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 transition-all duration-200"
                title="Toggle Sidebar"
            >
                <i id="sidebarToggleIcon" class="fa-solid fa-bars text-lg transition-transform duration-300"></i>
            </button>

        </div>


        {{-- NAVIGATION --}}
        <nav class="px-4 py-6 space-y-2">

            {{-- DASHBOARD --}}
            @if (in_array(Auth::user()->role, ['owner', 'kasir']))
                <a
                    href="{{ route('merchant.dashboard') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.dashboard')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <i class="fa-solid fa-chart-pie w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Dashboard
                    </span>
                </a>
            @endif


            {{-- KELOLA PESANAN --}}
            @if (in_array(Auth::user()->role, ['owner', 'kasir', 'dapur']))
                <a
                    href="{{ route('merchant.orders.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.orders.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <i class="fa-solid fa-cart-shopping w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Riwayat Pesanan
                    </span>
                </a>
            @endif


            {{-- PENGATURAN KAFE (Hanya Owner) --}}
            @if (Auth::user()->role === 'owner')

                <div class="pt-4 pb-1 sidebar-section">
                    <p class="px-4 text-[10px] font-black uppercase tracking-wider text-slate-500 sidebar-text whitespace-nowrap">
                        Pengaturan Kafe
                    </p>
                </div>

                {{-- QR CODE --}}
                <a
                    href="{{ route('merchant.qr.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.qr.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <i class="fa-solid fa-qrcode w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola QR Code
                    </span>
                </a>

                {{-- MENU --}}
                <a
                    href="{{ route('merchant.menu.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.menu.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <i class="fa-solid fa-utensils w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola Menu
                    </span>
                </a>

                {{-- STAF --}}
                <a
                    href="{{ route('merchant.staff.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.staff.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <i class="fa-solid fa-users-gear w-5 shrink-0"></i>
                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola Staf
                    </span>
                </a>

                {{-- PENGATURAN --}}
                <a
                    href="{{ route('merchant.settings.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.settings.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
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
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="truncate sidebar-user-info">
                    <p class="text-xs font-bold text-white truncate">
                        {{ Auth::user()->name }}
                    </p>
                    <span class="inline-block text-[9px] font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="p-1.5 text-slate-400 hover:text-rose-400 transition shrink-0 cursor-pointer"
                    title="Logout"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>

        </div>
    </div>

</aside>


{{-- SIDEBAR SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('sidebarToggleIcon');

        const sidebarBrand = document.getElementById('sidebarBrand');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const sidebarSections = document.querySelectorAll('.sidebar-section');
        const sidebarUserInfo = document.querySelector('.sidebar-user-info');
        const sidebarMenus = document.querySelectorAll('.sidebar-menu');

        let collapsed = localStorage.getItem('pesanin_sidebar_collapsed') === 'true';

        function updateSidebar() {
            if (collapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');

                if (sidebarBrand) {
                    sidebarBrand.classList.add('opacity-0', 'w-0', 'overflow-hidden');
                }

                sidebarTexts.forEach(function (text) {
                    text.classList.add('opacity-0', 'w-0', 'overflow-hidden');
                });

                sidebarSections.forEach(function (section) {
                    section.classList.add('hidden');
                });

                if (sidebarUserInfo) {
                    sidebarUserInfo.classList.add('opacity-0', 'w-0', 'overflow-hidden');
                }

                sidebarMenus.forEach(function (menu) {
                    menu.classList.remove('gap-3');
                    menu.classList.add('justify-center');
                    const icon = menu.querySelector('i');
                    if (icon) icon.classList.add('mx-auto');
                });

                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-xmark');
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');

                if (sidebarBrand) {
                    sidebarBrand.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
                }

                sidebarTexts.forEach(function (text) {
                    text.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
                });

                sidebarSections.forEach(function (section) {
                    section.classList.remove('hidden');
                });

                if (sidebarUserInfo) {
                    sidebarUserInfo.classList.remove('opacity-0', 'w-0', 'overflow-hidden');
                }

                sidebarMenus.forEach(function (menu) {
                    menu.classList.remove('justify-center');
                    menu.classList.add('gap-3');
                    const icon = menu.querySelector('i');
                    if (icon) icon.classList.remove('mx-auto');
                });

                toggleIcon.classList.remove('fa-xmark');
                toggleIcon.classList.add('fa-bars');
            }
        }

        updateSidebar();

        if (toggle) {
            toggle.addEventListener('click', function () {
                collapsed = !collapsed;
                localStorage.setItem('pesanin_sidebar_collapsed', collapsed);
                updateSidebar();
            });
        }
    });
</script>