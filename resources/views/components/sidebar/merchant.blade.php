<aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shrink-0 h-screen sticky top-0 border-r border-slate-800 select-none z-30">
    
    {{-- BAGIAN ATAS: Logo & Navigasi Menu --}}
    <div class="p-6 space-y-8 overflow-y-auto">
        
        {{-- Brand / Logo --}}
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shadow-amber-500/20">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <div>
                <h1 class="font-black text-white text-lg leading-tight tracking-tight">Command Center</h1>
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest">PesanIn Dashboard</p>
            </div>
        </div>

        {{-- Group Navigation --}}
        <nav class="space-y-6">
            
            {{-- Menu Utama --}}
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-2">Utama</p>
                
                <a href="{{ route('merchant.dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.dashboard') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie text-sm"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('merchant.orders.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.orders.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                    <span>Riwayat Pesanan</span>
<aside
    id="sidebar"
    class="w-64 bg-[#111827] text-slate-300 flex flex-col justify-between shrink-0 min-h-screen border-r border-slate-800 shadow-2xl transition-all duration-300 ease-in-out"
>

    {{-- HEADER --}}
    <div>

        <div
            class="px-5 py-5 flex items-center justify-between border-b border-slate-800/80"
        >

            {{-- LOGO PESANIN --}}
            <div
                id="sidebarLogo"
                class="flex items-center gap-3 overflow-hidden transition-all duration-300"
            >
                <div class="w-10 h-10 shrink-0 flex items-center justify-center">
                    <img
                        src="{{ asset('assets/images/menu-default.jpg') }}"
                        alt="PesanIn"
                        class="w-10 h-10 object-contain"
                    >
                </div>

                <div
                    id="sidebarBrand"
                    class="whitespace-nowrap transition-all duration-300"
                >
                    <h1 class="font-bold text-white text-base tracking-wide">
                        PesanIn
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
                <i
                    id="sidebarToggleIcon"
                    class="fa-solid fa-bars text-lg transition-transform duration-300"
                ></i>
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


            {{-- PESANAN --}}
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
                        Kelola Pesanan
                    </span>
                </a>
            </div>

            {{-- Pengaturan Kafe --}}
            <div class="space-y-1.5">
                <p class="px-3 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-2">Pengaturan Kafe</p>

                <a href="{{ route('merchant.qr.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.qr.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                    <span>Kelola QR Code</span>
                </a>

                <a href="{{ route('merchant.menu.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.menu.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-utensils text-sm"></i>
                    <span>Kelola Menu</span>
                </a>

                <a href="{{ route('merchant.staff.index') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs transition duration-150 {{ request()->routeIs('merchant.staff.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                    <span>Kelola Staf</span>
                </a>
            </div>

        </nav>
    </div>

    {{-- BAGIAN BAWAH (STICKY BOTTOM): User Profile & Logout --}}
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/50">
        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-800/50 border border-slate-800">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center font-bold text-xs shrink-0">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Owner PST' }}</p>
                    <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">{{ auth()->user()->role ?? 'Owner' }}</p>
                </div>
            </div>

            {{-- Form Logout --}}
            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" 
                        class="w-8 h-8 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 flex items-center justify-center transition"
                        title="Keluar / Logout">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                </button>
            </form>
        </div>
    </div>

</aside>

            {{-- PENGATURAN --}}
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


                {{-- STAFF --}}
                <a
                    href="{{ route('merchant.staff.index') }}"
                    class="sidebar-menu flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all duration-200
                    {{ request()->routeIs('merchant.staff.*')
                        ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30'
                        : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}"
                >
                    <i class="fa-solid fa-users-gear w-5 shrink-0"></i>

                    <span class="sidebar-text whitespace-nowrap transition-all duration-200">
                        Kelola Staf Kafe
                    </span>
                </a>

            @endif

        </nav>

    </div>


    {{-- USER PROFILE --}}
    <div class="p-4 border-t border-slate-800/80">

        <div
            class="flex items-center justify-between bg-slate-900/80 p-3 rounded-xl border border-slate-800"
        >

            <div class="flex items-center gap-3 overflow-hidden">

                <div
                    class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center font-bold text-xs shrink-0"
                >
                    <i class="fa-solid fa-user"></i>
                </div>

                <div class="truncate sidebar-user-info">

                    <p class="text-xs font-bold text-white truncate">
                        {{ Auth::user()->name }}
                    </p>

                    <span
                        class="inline-block text-[9px] font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20"
                    >
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>

                </div>

            </div>


            {{-- LOGOUT --}}
            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button
                    type="submit"
                    class="p-1.5 text-slate-400 hover:text-rose-400 transition shrink-0"
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

                // SIDEBAR KECIL
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');

                // Sembunyikan teks
                sidebarBrand.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );

                sidebarTexts.forEach(function (text) {
                    text.classList.add(
                        'opacity-0',
                        'w-0',
                        'overflow-hidden'
                    );
                });

                sidebarSections.forEach(function (section) {
                    section.classList.add('hidden');
                });

                sidebarUserInfo.classList.add(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );

                // Menu jadi center
                sidebarMenus.forEach(function (menu) {
                    menu.classList.remove('gap-3');
                    menu.classList.add('justify-center');

                    menu.querySelector('i').classList.add('mx-auto');
                });

                // Icon hamburger menjadi X
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-xmark');

            } else {

                // SIDEBAR NORMAL
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');

                // Tampilkan teks
                sidebarBrand.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );

                sidebarTexts.forEach(function (text) {
                    text.classList.remove(
                        'opacity-0',
                        'w-0',
                        'overflow-hidden'
                    );
                });

                sidebarSections.forEach(function (section) {
                    section.classList.remove('hidden');
                });

                sidebarUserInfo.classList.remove(
                    'opacity-0',
                    'w-0',
                    'overflow-hidden'
                );

                // Menu normal
                sidebarMenus.forEach(function (menu) {
                    menu.classList.remove('justify-center');
                    menu.classList.add('gap-3');

                    menu.querySelector('i').classList.remove('mx-auto');
                });

                // Icon X menjadi hamburger
                toggleIcon.classList.remove('fa-xmark');
                toggleIcon.classList.add('fa-bars');
            }
        }


        // Jalankan kondisi awal
        updateSidebar();


        // Toggle sidebar
        toggle.addEventListener('click', function () {

            collapsed = !collapsed;

            localStorage.setItem(
                'pesanin_sidebar_collapsed',
                collapsed
            );

            updateSidebar();

        });

    });

</script>
